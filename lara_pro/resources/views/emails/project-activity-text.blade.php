TURANCE TECHNOLOGIES
PROJECT WORKSPACE

{{ $activityLabel }}

Hi {{ \Illuminate\Support\Str::before($recipientName, ' ') }},

{{ $activityMessage }}

Project: {{ $project->name }}{{ $project->project_number ? ' ('.$project->project_number.')' : '' }}
Sent: {{ $sentAt->format('M j, Y · g:i A') }}

Open workspace: {{ $url }}

You’re receiving this because you’re part of this project workspace.

Turance Technologies
This is an automated project update. Replies to this email are not monitored.
