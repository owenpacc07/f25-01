import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileWriter;
import java.io.IOException;
import java.io.PrintWriter;
import java.util.ArrayList;
import java.util.Collections;
import java.util.Comparator;
import java.util.LinkedList;
import java.util.PriorityQueue;
import java.util.Queue;
import java.util.Scanner;

/*


PiD, Arrival, Burst, Priority 

-Non-preemptive
1 SJF: Shortest Job First 	-Finished
2 PH: Priority High (higher number means higher priority)
3 PL: Priority Low (lower number means higher priority)



-Preemptive-
4 RR: Round Robin
5 pSJF: Preemptive Shortest Job First
6 pPH: Preemptive Priority High (higher number means higher priority)
7 pPL: Preemptive Priority Low (lower number means higher priority)
*/
public class m006 {

  private static ArrayList<Process> process = new ArrayList<Process>(); // new created
  private static ArrayList<EndProcess> sProcess = new ArrayList<EndProcess>(); // writing to file

  // Shortest Job First
  private static PriorityQueue<Process> processSJFsort = new PriorityQueue<Process>(
      new Comparator<Process>() { // Preemptive
        public int compare(Process first, Process second) { // compares burst times
          return first.getBurst() - second.getBurst();
        }
      });

  private static StringBuffer buffer;
  public static int typeScheduler, cpuTime, quantum, waiting, stopCheck, size = 0;
  public static int endFlag = 0;

  public static void addToQueue() { // adds a process to a queue if the cpuTime meets arrival time
    for (int i = 0; i < process.size(); i++) {
      if (process.get(i).getArrive() == cpuTime) {
        processSJFsort.add(process.get(i));
        stopCheck++;
      }
    }
  }

  public static Process swapPre(Process p) { // swaps process with one waiting in queue (Preemptive)
    if (p.getBurst() > 0) { // if the process is not finished then we need to put it back in the queue
      processSJFsort.add(p);
    }

    // We need a new process to replace the one we put back in
    return processSJFsort.remove();
  }

  public static void CPU() {
    int start;
    Process p = null;
    addToQueue();
    cpuTime = 0;

    // Initial Process
    p = processSJFsort.remove();

    start = cpuTime;

    while (endFlag != 1 || cpuTime == 10000) {
      if (stopCheck <= size && cpuTime != 0)
        addToQueue();

      if (p.getBurst() == 0 ||
          !processSJFsort.isEmpty() &&
              processSJFsort.peek().getBurst() < p.getBurst()) { // if burst has ended
        EndProcess end = new EndProcess(p.getID(), start, cpuTime);
        sProcess.add(end);
        start = cpuTime;

        if (!processSJFsort.isEmpty())
          p = swapPre(p);
      }

      if (processSJFsort.isEmpty() && p.getBurst() == 0)
        endFlag = 1; // if all processes are accounted for and queue is empty we are done

      p.decBurst(); // removes 1 from burst
      cpuTime++; // adds to cpuTIme
    }
  }

  // updates flag file to have a value of 1
  static void updateFlagFile() {
    try {
      File flagFile = new File(
          "../../../files/core-c/m-006/flag-file.txt");
      FileWriter writer = new FileWriter(flagFile, false);
      writer.write("1");
      System.out.print("Flag File Changed to 1");
      writer.close();
    } catch (IOException e) {
      System.out.println("Error in updateFlagFile()");
    }
  }

  public static void main(String args[]) throws IOException {
    System.out.println("Test if working properly");
    inFile(); // reads from file
    size = process.size();
    CPU(); // CPU scheduler
    outFile(); // outputs to file
    updateFlagFile(); // updates flag file
  }

  public static void inFile() throws FileNotFoundException {
    buffer = new StringBuffer();
    File inFile = new File("../../../files/core-c/c-cpu/in-cpu.dat");
    Scanner in = new Scanner(inFile);

    int pID, pArrive, pBurst, pPriority, i = 0;
    String line;

    // Hardcode scheduler type for pSJF (no quantum needed)
    typeScheduler = 5; // Fixed for m006
    buffer.setLength(0);

    while (in.hasNextLine()) {
        i = 0;
        line = in.nextLine();

        // Process ID------------------------//
        while (i < line.length() && line.charAt(i) != ',') {
            buffer.append(line.charAt(i));
            i++;
        }
        pID = Integer.parseInt(buffer.toString().trim());
        buffer.setLength(0);
        i++; // Skip comma

        // Process Arrival Time------------------------//
        while (i < line.length() && line.charAt(i) != ',') {
            buffer.append(line.charAt(i));
            i++;
        }
        pArrive = Integer.parseInt(buffer.toString().trim());
        buffer.setLength(0);
        i++; // Skip comma

        // Process Burst------------------------//
        while (i < line.length() && line.charAt(i) != ',') {
            buffer.append(line.charAt(i));
            i++;
        }
        pBurst = Integer.parseInt(buffer.toString().trim());
        buffer.setLength(0);
        i++; // Skip comma

        // Process Priority------------------------//
        while (i < line.length()) {
            buffer.append(line.charAt(i));
            i++;
        }
        pPriority = Integer.parseInt(buffer.toString().trim());
        buffer.setLength(0);

        // Add process to list
        Process e = new Process(pID, pArrive, pBurst, pPriority);
        process.add(e);
    }
    in.close();

    // Debug: Verify processes loaded
    System.out.println("Processes loaded: " + process.size());
    for (Process proc : process) {
        System.out.println(proc);
    }
}

  public static void outFile() throws IOException {
    File outFile = new File(
        "../../../files/core-c/m-006/out-006.dat");
    PrintWriter out = new PrintWriter(outFile);
    String scheduler = null;

    scheduler = "Shortest job First(Preemptive)";

    out.println("Type of Scheduler: " + scheduler);
    out.println("Number of Processes: " + size);
    if (typeScheduler == 4)
      out.println("Quantum time: " + quantum);

    out.println("");

    for (int i = 0; i < sProcess.size(); i++)
      out.println( // loop to write processes to file
          (sProcess.get(i).getID()) +
              "," +
              (sProcess.get(i).getStart()) +
              "," +
              (sProcess.get(i).getEnd()));

    out.println("");

    int duration = process.stream().mapToInt(p -> p.getOriginalBurstTime()).sum();
		for (int timeStep = 0; timeStep <= duration; timeStep++){
			StringBuffer line = new StringBuffer();
			line.append(timeStep + ",");

			int currentCpuProcessID = 0;
			for(EndProcess proc: sProcess){
				if(timeStep >= proc.getStart() && timeStep < proc.getEnd()) {
					currentCpuProcessID = proc.getID();
					break;
				}
			}

			// print out p1-p4 remaining burst time
			// check arrival times
			for (Process proc: process){
				if (proc.getArrive() > timeStep){
					line.append("-,");
				}else {
					if (currentCpuProcessID == proc.getID()) {
						line.append(proc.getBurstTimeRemaining() + ",");
						proc.CpuBurst();
						if (proc.getBurstTimeRemaining() == 0) {
							proc.finish();
						}
					}
					else {
						line.append(proc.getBurstTimeRemaining() + ",");
						if (timeStep != duration && !proc.isFinished()){
							proc.incrementWaitingTime();
						}
					}
				}
			}

      // print CPUs current process
			line.append("P" + currentCpuProcessID + ",");

			// print waiting times for each proc
			for(Process proc: process){
				line.append(proc.getWaitingTime() + ",");
			}

			// print processes waiting in queue
			for (Process proc: process){
				if (!proc.isFinished() && currentCpuProcessID != proc.getID() && timeStep >= proc.getArrive()){
					line.append("P"+proc.getID() + " ");
				}
			}

			out.println(line.toString());
		}
    out.close();
  }
}

class EndProcess implements Comparable<EndProcess> {

  private int id, start, end;

  public EndProcess(int id, int start, int end) {
    this.id = id;
    this.start = start; // equivalent to wait
    this.end = end; // equivalent to wait
  }

  public int getID() {
    return id;
  }

  public int getStart() {
    return start;
  }

  public int getEnd() {
    return end;
  }

  public int compareTo(EndProcess proc) {
    if (this.id > proc.id) {
      return 1;
    } else if (this.id < proc.id) {
      return -1;
    } else
      return 0;
  }

  public String toString() {
    StringBuffer info = new StringBuffer();
    info.append("\nProcess ID : " + id);
    info.append("\nStart Time : " + start);
    info.append("\nEnd Time : " + end);
    return info.toString();
  }
}

class Process {

	private int id, arrive, burst, priority, originalBurstTime;
	private int burstTimeRemaining, totalWaitingTime;
	private boolean isFinished;

	public Process(int id, int arrive, int burst, int priority) {
		this.id = id;
		this.arrive = arrive;
		this.burst = burst;
		this.originalBurstTime = burst;
		this.burstTimeRemaining = burst;
		this.priority = priority;
		this.totalWaitingTime = 0;
		this.isFinished = false;
	}

	public int getID() {
		return id;
	}

	public int getArrive() {
		return arrive;
	}

	public int getBurst() {
		return burst;
	}

	public int getOriginalBurstTime(){
		return this.originalBurstTime;
	}

	public int getBurstTimeRemaining(){
		return this.burstTimeRemaining;
	}

	public int getWaitingTime(){
		return this.totalWaitingTime;
	}

	public boolean isFinished(){
		return this.isFinished;
	}

	public void finish(){
		this.isFinished = true;
	}

	public int decBurst() {
		return burst = burst - 1;
	}

	public int getPri() {
		return priority;
	}

	public void CpuBurst(){
		this.burstTimeRemaining--;
	}

	public void incrementWaitingTime(){
		this.totalWaitingTime++;
	}

	public String toString() {
		return "Process: " + getID() + ", Arrival Time:" + getArrive() + ", Burst time:" + getBurst() + ", Priority: "
				+ getPri();
	}
}
