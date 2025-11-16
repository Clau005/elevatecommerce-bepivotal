<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $theme->name }} - Visual Editor</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @vite(['resources/css/app.css'])
    
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        
        #editor-app {
            height: 100vh;
            width: 100vw;
        }
    </style>
</head>
<body>
    <div id="editor-app">
        <visual-editor
            :theme='@json($theme)'
            :page='@json($page ?? null)'
            :template='@json($template ?? null)'
            :available-sections='@json($availableSections)'
            :is-template='@json(isset($template))'
        ></visual-editor>
    </div>

    @vite(['packages/elevatecommerce/editor/resources/js/editor.js'])
</body>
</html>
