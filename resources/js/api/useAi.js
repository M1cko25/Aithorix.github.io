import { ref } from 'vue';
import axios from 'axios';

export function useAI() {
    const response = ref('');
    const isLoading = ref(false);
    const error = ref(null);

    const projectId = ref(null);

    const sendPrompt = async (prompt, projectId) => {
        isLoading.value = true;
        error.value = null;
        
        try {
            // First, get project data if a project ID is provided
            let projectData = null;
            if (projectId) {
                try {
                    const { data } = await axios.post('/ai/getProjectData', { projectId });
                    projectData = data;
                } catch (err) {
                    console.error('Error fetching project data:', err);
                    // Continue without project data if there's an error
                }
            }

            // Create a context-enriched prompt
            let enrichedPrompt = `You are an AI assistant for a project management tool named Aithorix and
                your name is Lira.
                Lira means "Lightweight Intelligent Real-time Assistant".
                You are a helpful assistant that can help the user with their project management tasks.
                You are also able to help the user with their questions and concerns about project management.
                You are unable to help about outside topics of project management.`;

            // Add project context if available
            if (projectData && !projectData.error) {
                enrichedPrompt += `\n\nCURRENT PROJECT CONTEXT:
                Project Name: ${projectData.project.name}
                Project Key: ${projectData.project.key}
                
                Epics (${projectData.epics.length}):
                ${projectData.epics.map(epic => `- ${epic.name} (Status: ${epic.status || 'Not started'})`).join('\n')}
                
                Number of Tasks: ${projectData.backlogs.length}
                ${projectData.backlogs.length > 0 ? 
                    `Common Task Statuses: ${[...new Set(projectData.backlogs.map(b => b.status))].join(', ')}` : ''}

                List of task and their statuses: ${projectData.backlogs.map(b => `- ${b.name} (Status: ${b.status || 'Not started'})`).join('\n')}
                Tasks by Epic:\n${projectData.epics.map(epic => {
                    const epicTasks = projectData.backlogs.filter(task => task.epic_id === epic.id);
                    return `- ${epic.name} (${epicTasks.length} tasks):\n  ${epicTasks.map(task => 
                        `  • ${task.title || 'Untitled Task'} (Status: ${task.status || 'Not started'})`
                    ).join('\n')}`;
                }).join('\n')}
                Task Columns: ${projectData.columns.map(col => col.title).join(', ')}
                
                Sprints: ${projectData.sprints.length > 0 ? 
                    `${projectData.sprints.map(s => `- ${s.name || 'Sprint ' + s.id} (${s.status || 'Not started'})`).join('\n')}` : 
                    'No active sprints'}
                
                When responding, use this project information to provide context-relevant answers.
                `;
            }
            
            // Add the user's actual prompt
            enrichedPrompt += `\n\nNow you are in the following conversation:\n${prompt}`;

            // Send the enriched prompt to the AI
            const { data } = await axios.post('/ai/process', { 
                prompt: enrichedPrompt
            });
            
            if (data.error) {
                error.value = data.error;
                return null;
            }
            
            const aiResponse = data.candidates?.[0]?.content?.parts?.[0]?.text;
            if (aiResponse) {
                response.value = aiResponse;
                return aiResponse;
            } else {
                error.value = 'No response from AI';
                return null;
            }
        } catch (err) {
            console.error('AI Error:', err);
            error.value = err.response?.data?.error || 'Error processing AI request';
            return null;
        } finally {
            isLoading.value = false;
        }
    };

    return { response, isLoading, error, sendPrompt };
}
