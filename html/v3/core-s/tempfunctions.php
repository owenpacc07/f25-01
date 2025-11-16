    <?php
    // this file is for intialziing temp user session files for editing I/O, the temp files are assigned to current sessionID and are destroyed when session ends
     if (!isset($_SESSION['temp_files'])) {
        $_SESSION['temp_files'] = array();
    }
    
    function create_temp_file($original_file) {
        $temp_dir = sys_get_temp_dir();
        $temp_file = tempnam($temp_dir, 'tmp');
        copy($original_file, $temp_file);
        return $temp_file;
    }
    //creates path and temp file/directory
    function get_temp_file_path($original_file) {
        
        if (!isset($_SESSION['temp_files'][$original_file])) {
             $_SESSION['temp_files'][$original_file] = create_temp_file($original_file);
        }
        return $_SESSION['temp_files'][$original_file];
    }
    //produce string output of file, preserving  format
    function stringify_file_content($temp_file) {
        $content = file_get_contents($temp_file);
        return nl2br(htmlspecialchars($content));
    }
            
    ?>