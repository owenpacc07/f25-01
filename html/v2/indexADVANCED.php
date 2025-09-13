<!DOCTYPE html>
<html lang="en">


<head>
  <title>OS Visualizations</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
   
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="./styles.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</head>

<body>

  <?php include './navbar.php'; ?>

  <div class="bg">



    <div class="container p-3 my-3 border bg-light rounded">
      <div class="pt-3 pr-3 pl-3">

        <h1 id="title">WEBVIZ OS</h1>

      </div>

      <h3 class="description">Select from an OS process below to see it visualized!</h3>
      <hr>

      <div class="container-fluid">

      </div>

      <h4>Process Management</h4>
      <div class="row">
        <div class="col-md-4 card">
          <div class="card-body">
            <a class="card-title rounded-4" href="./1-cpu" style="float: right;">CPU Scheduling</a>
          </div>
          <div class="card-body">
            <p class="card-text">An animation which shows off various CPU process scheduling alogrithms, including FCFS, SJF, Priority, and RR.</p>
          </div>
        </div>
        <div class="col-md-4 card">
          <div class="card-body">
            <a class="card-title rounded-4" href="./p1-process-states" style="float: right;">p1 - Process States</a>
          </div>
          <div class="card-body">
            <p class="card-text">An animation which takes you through the process scheduling state diagram.</p>
          </div>
        </div>
        <div class="col-md-4 card">
          <div class="card-body">
            <a class="card-title rounded-4" href="./p2-deadlock" style="float: right;">p2 - Deadlock</a>
          </div>
          <div class="card-body">
            <p class="card-text">A handful of animations which visulize common CPU deadlocking problems.</p>
          </div>
        </div>
      </div>
      <hr>
      <h4>Memory Management</h4>
      <div class="row">
        <div class="col-md-4 card">
          <div class="card-body">
            <a class="card-title rounded-4" href="./6-address" style="float: right;">Address Translation</a>
          </div>
          <div class="card-body">
            <p class="card-text">An animation showing how the OS looks up the physics memory address matching a virtual address.</p>
          </div>
        </div>
        <div class="col-md-4 card">
          <div class="card-body">
            <a class="card-title rounded-4" href="./4-memory-allocation" style="float: right;">Memory Allocation</a>
          </div>
          <div class="card-body">
            <p class="card-text">An animation showing how the OS allocates processes with different allocation algorithms such as first, best, and worst fit.</p>
          </div>
        </div>
        <div class="col-md-4 card">
          <div class="card-body">
            <a class="card-title rounded-4" href="./2-replace" style="float: right;">Page Replacement</a>
          </div>
          <div class="card-body">
            <p class="card-text">An animation which visulizes the process of page replacement</p>
          </div>
        </div>
        <div class="col-md-4 card">
          <div class="card-body">
            <a class="card-title rounded-4" href="./p4-memory-access" style="float: right;">p4 - Memory Access</a>
          </div>
          <div class="card-body">
            <p class="card-text">An animation which visulizes the process of Memory Access</p>
          </div>
        </div>
      </div>
      <hr>
      <h4>I/O Management</h4>
      <div class="row">
        <div class="col-md-4 card">
          <div class="card-body">
            <a class="card-title rounded-4" href="./p5-io-cycle" style="float: right;">I/O Cycle</a>
          </div>
          <div class="card-body">
            <p class="card-text">An animation which visulizes the process of the I/O cycle</p>
          </div>
        </div>
      </div>
      <div class="col-md-4 card">
        <div class="card-body">
          <a class="card-title rounded-4" href="./5-files" style="float: right;">File Allocation</a>
        </div>
        <div class="card-body">
          <p class="card-text">An animation which visulizes the process of file allocation</p>
        </div>
      </div>
      <div class="col-md-4 card">
        <div class="card-body">
          <a class="card-title rounded-4" href="./3-disk" style="float: right;">Disk Scheduling</a>
        </div>
        <div class="card-body">
          <p class="card-text">An animation which visulizes the process of disk scheduling</p>
        </div>
      </div>
      <hr>
      <h4>Security Management</h4>
      <div class="row">
        <div class="col-md-4 card">
          <div class="card-body">
            <a class="card-title rounded-4" href="./p6-access-control" style="float: right;">p6 - Access Control</a>
          </div>
          <div class="card-body">
            <p class="card-text">An animation which visulizes how the OS handles access control</p>
          </div>
        </div>
      </div>
    </div>

    <br>
    <br>

    <footer class="bg-light text-center text-lg-start">

      <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
        <p>Fall 2022 Contributors: Mark Venuto | Tim Haines </p>
        <p>Spring 2022 Contributors: Ryan Arnold | Christopher Brady | Maria Hernandez | Jordon Roberts | Jenna Rodriguez | Huaqi Zhang | Adrian Francis | Alec Lehmphul | Mitchell Chappell</p>
        <p>Fall 2021 Contributors: Matthew Morfea | Henry Murillo | Charles Agudelo | Joshua Morris | Tevin Skeete | Aaron Traver </p>
      </div>

    </footer>



</body>

</html>