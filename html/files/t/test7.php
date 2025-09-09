<!DOCTYPE html>
<html>
<body>

<h1>PHP calls Java</h1>

<?php
echo exec('java /var/www/projects/a00/var/test2.java');

echo " ... [Calling (remote/global)JAVA is not working IF nothing is IN FRONT of this]";
?>

</body>
</html>
