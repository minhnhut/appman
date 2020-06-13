<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/assets/base.css">
    <link rel="stylesheet" href="/assets/miligram.css">
    <script src="/assets/zepto.js"></script>
    <title>AppMan 1.0</title>
</head>
<body>
    <div class="row" id="tool-container">
        <div class="column" style="width: 300px; position: relative;">
            <div class="tool-panel">
                <div id="tool-name">AppMan <small>v1.0</small></div>
                <ul class="menu">
                    <li>
                        <a href="/"><img src="/assets/img/apps.png" alt="apps" width="32px"> Apps</a>
                    </li>
<!--                    <li>-->
<!--                        <a href="/settings">-->
<!--                            <img src="/assets/img/settings.png" alt="settings" width="32px">-->
<!--                            Settings-->
<!--                        </a>-->
<!--                    </li>-->
                    <li>
                        <a href="/logout">
                            <img src="/assets/img/settings.png" alt="settings" width="32px">
                            Logout
                        </a>
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

    <?=$this->section('js')?>
</body>
</html>
