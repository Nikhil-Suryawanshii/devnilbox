<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .content { margin-bottom: 20px; }
        .field { margin-bottom: 10px; }
        .label { font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>New Contact Us Message</h2>
        </div>
        
        <div class="content">
            <div class="field">
                <span class="label">Name:</span> {{ $data['name'] ?? 'N/A' }}
            </div>
            <div class="field">
                <span class="label">Email:</span> {{ $data['email'] ?? 'N/A' }}
            </div>
            <div class="field">
                <span class="label">Phone:</span> {{ $data['phone'] ?? 'N/A' }}
            </div>
            <div class="field">
                <span class="label">Subject:</span> {{ $data['subject'] }}
            </div>
            
            <hr>
            
            <div class="field">
                <span class="label">Message:</span>
                <p style="white-space: pre-wrap;">{{ $data['message'] }}</p>
            </div>
        </div>
    </div>
</body>
</html>
