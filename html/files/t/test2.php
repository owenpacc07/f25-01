<!DOCTYPE html>
<html>
<body>

<h1>PHP calls Java</h1>

<?php
echo exec('java /var/www/projects/s23-02/html/cgi-bin/t/test.java');

echo " ... [Calling (remote/global)JAVA is not working IF nothing is IN FRONT of this]";
?>

</body>
</html>