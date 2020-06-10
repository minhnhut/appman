<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/assets/base.css">
    <link rel="stylesheet" href="/assets/miligram.css">
    <title>DepMan</title>
</head>
<body>
    <div class="row" id="tool-container">
        <div class="column" style="width: 300px; position: relative;">
            <div class="tool-panel">
                <div id="tool-name">AppMan</div>
                <ul>
                    <li>
                        <a href="/">Apps</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="column">
            <div style="padding: 10px">
                <?=$this->section('content');?>
            </div>
        </div>
    </div>
</body>
</html>
