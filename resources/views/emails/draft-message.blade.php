<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Newsletter Draft</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .draft-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .draft-icon {
            font-size: 48px;
            color: #fbbf24;
            margin-bottom: 20px;
        }
        .draft-title {
            color: #f59e0b;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .draft-message {
            color: #6b7280;
            margin-bottom: 30px;
        }
        .btn {
            display: inline-block;
            background-color: #3b82f6;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
        }
        .btn:hover {
            background-color: #2563eb;
        }
    </style>
</head>
<body>
    <div class="draft-container">
        <div class="draft-icon">📝</div>
        <h1 class="draft-title">Newsletter Draft</h1>
        <p class="draft-message">
            <strong>{{ $stepTitle }}</strong> (Step {{ $stepOrder }}) is currently in draft mode and not available for preview.
        </p>
        <p class="draft-message">
            To publish this newsletter or edit its content, please visit the newsletter steps management page.
        </p>
        <a href="/newsletter-steps" class="btn">Manage Newsletter Steps</a>
    </div>
</body>
</html>