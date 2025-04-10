<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Task Assigned</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9fafb;
            color: #1f2937;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #e5e7eb;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 20px;
        }
        .content {
            padding: 20px 0;
        }
        h1 {
            color: #4f46e5;
            font-size: 24px;
            margin-bottom: 20px;
        }
        h2 {
            font-size: 18px;
            margin-top: 0;
            margin-bottom: 10px;
        }
        .task-details {
            background-color: #f3f4f6;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .task-details p {
            margin: 8px 0;
        }
        .button {
            display: inline-block;
            background-color: #4f46e5;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
            font-weight: 600;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 14px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Task Assigned to You</h1>
        </div>

        <div class="content">
            <p>Hello,</p>

            <p><strong>{{ $assignedBy->name }}</strong> has assigned you a task in project <strong>{{ $projectName }}</strong>.</p>

            <div class="task-details">
                <h2>{{ $task->title }}</h2>
                <p><strong>Task ID:</strong> {{ $task->key }}</p>
                <p><strong>Type:</strong> {{ $task->type }}</p>
                <p><strong>Status:</strong> {{ $task->status }}</p>
                <p><strong>Priority:</strong> {{ $task->priority }}</p>

                @if($task->description)
                <p><strong>Description:</strong><br>{{ $task->description }}</p>
                @endif
            </div>

            <p>You can view and update this task in your project dashboard.</p>

            <a href="{{ url('/scrum/board?id=' . $task->project_id) }}" class="button">View Task</a>
        </div>

        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} Aithorix. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
