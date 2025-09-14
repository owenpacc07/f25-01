<!DOCTYPE html>
<html>
    <head>
    <title>Memory Layout</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/konva@8.3.3/konva.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </head>

    <body>
         <?php include '../navbar.php'; ?>
         
         <br>
         <h1 class="text-center">Memory Layout</h1> 
         <hr>
         <br>
         <br>
         <h2 class="text-center">Program Memory Management Visualization</h2>
         
         <!-- Java Source Code Example Panel -->
         <div class="container mb-4">
             <div class="row">
                 <div class="col-md-8 offset-md-2">
                     <div class="card code-example-card">
                         <div class="card-header bg-primary text-white">
                             <h5 class="mb-0">Example Java Source Code (A.java)</h5>
                         </div>
                         <div class="card-body">
                             <div class="file-path mb-2">
                                 <i class="fa fa-folder-open"></i> DISK C:/MyFolder/A.java
                             </div>
                             <pre class="java-code-block"><code>public class A {
    static int x = 1000;
    
    public static int isum(int i1, int i2) 
    {
        int sum = x;
        for (int i = 0; il; i <= i2; i++)
            sum += i;
        return sum;
    
    }
    
    public static void main(String[] args) 
    {
        String sss = "The Sum from ";
        System.out.println(sss + "1 to 10 is " + (isum(1, 10) - x) );
        System.out.println(sss + "20 to 30 is " + (isum(20, 30) - x) );
        System.out.println(sss + "35 to 45 is " + (isum(35, 45) - x) );
    }   
}</code></pre>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
         
         <!-- Animation Controls -->
         <div class="form-check text-center">
            <input class="form-check-input" type="radio" name="animationType" id="stepByStep" value="StepByStep">
            <label class="form-check-label" for="stepByStep">Step-by-Step</label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input class="form-check-input" type="radio" name="animationType" id="automatic" value="Automatic" checked>
            <label class="form-check-label" for="automatic">Automatic</label>
         </div>
         <br>
         
         <div class="text-center">
            <button type="button" class="btn btn-primary" id="start">Start</button>
            <button type="button" class="btn btn-primary" id="back">Back</button>
            <button type="button" class="btn btn-primary" id="play">Play</button>
            <button type="button" class="btn btn-primary" id="next">Next</button>
            <button type="button" class="btn btn-primary" id="end">End</button>
            <button type="button" class="btn btn-primary" id="reset">Reset</button>
         </div>
         <br>
         
         <!-- Loading Overlay -->
         <div id="overlay" class="overlay">
             <div class="spinner-border text-primary" role="status">
                 <span class="sr-only">Loading...</span>
             </div>
             <p>Loading animation...</p>
         </div>
         
         <!-- Memory Layout Visualization Area -->
         <div id="animarea" class="container">
             <div class="row">
                 <div class="col-md-6 memory-section">
                     <h4 class="text-center">Java Program Compilation</h4>
                     <div id="processarea" class="process-container">
                         <div class="step-box">
                             <div class="step-number">1</div>
                             <div class="step-content">Source Code (.java)</div>
                         </div>
                         <div class="arrow-down"></div>
                         <div class="step-box">
                             <div class="step-number">2</div>
                             <div class="step-content">Compiler (javac)</div>
                         </div>
                         <div class="arrow-down"></div>
                         <div class="step-box">
                             <div class="step-number">3</div>
                             <div class="step-content">Bytecode (.class)</div>
                         </div>
                     </div>
                 </div>
                 <div class="col-md-6 memory-section">
                     <h4 class="text-center">Memory Structure</h4>
                     <div class="memory-layout">
                         <div class="mem-section" id="code-segment">
                             <div class="mem-label">Code Segment</div>
                             <div class="mem-content">Program instructions</div>
                         </div>
                         <div class="mem-section" id="data-segment">
                             <div class="mem-label">Data Segment</div>
                             <div class="mem-content">Static/global variables</div>
                         </div>
                         <div class="mem-section" id="heap">
                             <div class="mem-label">Heap</div>
                             <div class="mem-content">Dynamic memory allocation</div>
                         </div>
                         <div class="mem-section" id="stack">
                             <div class="mem-label">Stack</div>
                             <div class="mem-content">Local variables, function calls</div>
                         </div>
                     </div>
                 </div>
             </div>
             
             <div class="row mt-4">
                 <div class="col-12">
                     <h4 class="text-center">Memory Allocation Timeline</h4>
                     <table id="memorytable" class="memory-table">
                         <tbody>
                             <!-- Memory blocks will be populated by JavaScript -->
                         </tbody>
                     </table>
                 </div>
             </div>
             
             <div id="description" class="text-center mt-3 p-3 bg-light">
                 <p>Memory layout visualization shows how a program is organized in memory during execution.</p>
                 <div id="step-info">Step: 0 - Initial state</div>
             </div>
         </div>
         
         <!-- JavaScript Files -->
         <script src="js/load_data.js"></script>
         <script src="js/animations.js"></script>
    </body>
</html>

