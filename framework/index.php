<?php
require_once 'site.php';

\Core\Page\PageBootstrap::Start();
?>
<div class="container">
<h2>Main page</h2>

<h4>Example Application - class TestApp:</h4>
<ul>
    <li><a href="/TestApp">TestApp</a></li>
    <li><a href="/TestApp/StaticMethod_1">TestApp/StaticMethod_1</a></li>
    <li><a href="/TestApp/StaticMethod_2/MyParam1/MyParam2">TestApp/StaticMethod_2/MyParam1/MyParam2</a></li>
    <li><a href="/TestApp/1">TestApp/1</a> <span class="glyphicon glyphicon-info-sign" aria-hidden="true" title="You must have table in DB. (see PHPDocTag in /classes/App/TestApp/TestApp.php)"></span></li>
    <li><a href="/TestApp/1/ObjectMethod_1">TestApp/1/ObjectMethod_1</a> <span class="glyphicon glyphicon-info-sign" aria-hidden="true" title="You must have table in DB. (see PHPDocTag in /classes/App/TestApp/TestApp.php)"></span></li>
    <li><a href="/TestApp/1/ObjectMethod_2/MyParam1/MyParam2">TestApp/1/ObjectMethod_1/MyParam1/MyParam2</a> <span class="glyphicon glyphicon-info-sign" aria-hidden="true" title="You must have table in DB. (see PHPDocTag in /classes/App/TestApp/TestApp.php)"></span></li>
</ul>
</div>
<?php
\Core\Page\PageBootstrap::Finish();
