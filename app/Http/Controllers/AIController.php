<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Project;
use App\Models\Backlogs;
use App\Models\TaskStatusCol;
use App\Models\TaskComments;
use App\Models\User;
use App\Models\TaskAssignees;
use App\Models\Epics;
use App\Models\Sprints;


class AIController extends Controller
{
    public function processPrompt(Request $request)
    {
        try {
            $apiKey = env('GEMINI_API_KEY');
            
            if (!$apiKey) {
                Log::error('Gemini API key is not set');
                return response()->json([
                    'error' => 'API key is not configured'
                ], 500);
            }

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $request->input('prompt')]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 2048,
                ]
            ]);

            if (!$response->successful()) {
                Log::error('Gemini API error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json([
                    'error' => 'Failed to get AI response'
                ], $response->status());
            }

            $data = $response->json();
            
            // Extract the response text from the correct path in the response
            $responseText = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            
            if (!$responseText) {
                Log::error('Invalid response format from Gemini API', ['response' => $data]);
                return response()->json([
                    'error' => 'Invalid response format from AI'
                ], 500);
            }

            // Process the response text to preserve formatting
            $formattedText = $this->formatResponse($responseText);

            return response()->json([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => $formattedText]
                            ]
                        ]
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('AI processing error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error processing request'
            ], 500);
        }
    }

    private function formatResponse($text)
    {
        // Preserve markdown-style formatting
        $text = preg_replace('/\n\s*\n/', "\n\n", $text); // Preserve paragraph breaks
        $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text); // Bold text
        $text = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $text); // Italic text
        
        // Preserve code blocks and indentation
        $text = preg_replace('/```(.*?)```/s', '<pre><code>$1</code></pre>', $text);
        $text = preg_replace('/`(.*?)`/', '<code>$1</code>', $text);
        
        // Preserve lists
        $text = preg_replace('/^\s*[-*]\s/m', '• ', $text); // Convert list markers to bullets
        
        // Ensure proper spacing around headings
        $text = preg_replace('/#{1,6}\s*(.+)/', "\n$0\n", $text);
        
        return trim($text);
    }

    public function getProjectData(Request $request)
    {
        try {
            $projectId = $request->input('projectId');
            
            if (!$projectId) {
                return response()->json([
                    'error' => 'Project ID is required'
                ], 400);
            }

            $project = Project::where('id', $projectId)->first();
            
            if (!$project) {
                return response()->json([
                    'error' => 'Project not found'
                ], 404);
            }
            
            // Get all epics for this project
            $epics = Epics::where('project_id', $projectId)->get();
            
            // Get backlogs and sprints for each epic
            $backlogs = [];
            $sprints = [];
            
            foreach ($epics as $epic) {
                // Get backlogs (tasks) for this epic
                $epicBacklogs = Backlogs::where('epic_id', $epic->id)->get();
                $backlogs = array_merge($backlogs, $epicBacklogs->toArray());
                
                // Get sprints for this epic
                $epicSprints = Sprints::where('epic_id', $epic->id)->get();
                $sprints = array_merge($sprints, $epicSprints->toArray());
            }
            
            // Get project columns (task statuses)
            $columns = TaskStatusCol::where('project_id', $projectId)->get();
            
            return response()->json([
                'project' => $project,
                'epics' => $epics,
                'backlogs' => $backlogs,
                'sprints' => $sprints,
                'columns' => $columns
            ]);
        } catch (\Exception $e) {
            Log::error('AI project data error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error retrieving project data'
            ], 500);
        }
    }
}

