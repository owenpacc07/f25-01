import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileWriter;
import java.io.IOException;
import java.io.PrintWriter;
import java.util.ArrayList;
import java.util.Comparator;
import java.util.PriorityQueue;
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
public class m008 {

  private static ArrayList<Process> process = new ArrayList<Process>(); // new created
  private static ArrayList<EndProcess> sProcess = new ArrayList<EndProcess>(); // writing to file

  // Priority High to Low(High number means high priority
  private static PriorityQueue<Process> processHLsort = new PriorityQueue<Process>(
      new Comparator<Process>() { // Preemptive
        public int compare(Process first, Process second) {
          return second.getPri() - first.getPri();
        }
      });

  // Priority Low to High(High number means low priority)
  private static PriorityQueue<Process> processLHsort = new PriorityQueue<Process>(
      new Comparator<Process>() {
        public int compare(Process first, Process second) {
          return first.getPri() - second.getPri();
        }
      });

  private static StringBuffer buffer;
  public static int typeScheduler, cpuTime, quantum, waiting, stopCheck, size = 0;
  public static int endFlag = 0;

  public static void addToQueue() { // adds a process to a queue if the cpuTime meets arrival time
    for (int i = 0; i < process.size(); i++) {
      if ((typeScheduler == 2 || typeScheduler == 6) &&
          process.get(i).getArrive() == cpuTime) {
        processHLsort.add(process.get(i));
        stopCheck++;
      }

      if ((typeScheduler == 3 || typeScheduler == 7) &&
          process.get(i).getArrive() == cpuTime) {
        processLHsort.add(process.get(i));
        stopCheck++;
      }
    }
  }

  public static Process swap(Process p) { // swaps process with one waiting in queue (Non-Preemptive)
    if (typeScheduler == 2)
      return processHLsort.remove();
    else
      return processLHsort.remove(); // P-HL //typeScheduler == 3 P-LH
  }

  public static Process swapPre(Process p) { // swaps process with one waiting in queue (Preemptive)
    if (p.getBurst() > 0) { // if the process is not finished then we need to put it back in the queue
      if (typeScheduler == 6)
        processHLsort.add(p);
      else
        processLHsort.add(p); // P-HL //typeScheduler == 7 P-LH
    }

    // We need a new process to replace the one we put back in
    if (typeScheduler == 6)
      return processHLsort.remove();
    else
      return processLHsort.remove(); // P-HL //(typeScheduler == 7 ) //P-LH
  }

  public static void CPU() {
    int start;
    Process p = null;
    addToQueue();
    cpuTime = 0;

    // Initial Process
    if (typeScheduler == 2 || typeScheduler == 6)
      p = processHLsort.remove(); // NP-HL //P-HL
    if (typeScheduler == 3 || typeScheduler == 7)
      p = processLHsort.remove(); // NP-LH //P-LH

    start = cpuTime;

    while (endFlag != 1 || cpuTime == 10000) {
      if (stopCheck <= size && cpuTime != 0)
        addToQueue();

      if (typeScheduler == 2) { // NP-PH
        if (p.getBurst() == 0) { // if burst has ended
          EndProcess end = new EndProcess(p.getID(), start, cpuTime);
          sProcess.add(end);
          start = cpuTime;

          if (!processHLsort.isEmpty())
            p = swap(p);
        }

        if (processHLsort.isEmpty() && p.getBurst() == 0)
          endFlag = 1; // if all processes are accounted for and queue is empty we are done

        p.decBurst(); // removes 1 from burst
      }

      if (typeScheduler == 3) { // NP-PL
        if (p.getBurst() == 0) { // if burst has ended
          EndProcess end = new EndProcess(p.getID(), start, cpuTime);
          sProcess.add(end);
          start = cpuTime;

          if (!processLHsort.isEmpty())
            p = swap(p);
        }

        if (processLHsort.isEmpty() && p.getBurst() == 0)
          endFlag = 1; // if all processes are accounted for and queue is empty we are done

        p.decBurst(); // removes 1 from burst
      }

      if (typeScheduler == 6) { // P-PH
        if (p.getBurst() == 0 ||
            !processHLsort.isEmpty() &&
                processHLsort.peek().getPri() > p.getPri()) { // if burst has ended
          EndProcess end = new EndProcess(p.getID(), start, cpuTime);
          sProcess.add(end);
          start = cpuTime;

          if (!processHLsort.isEmpty())
            p = swapPre(p);
        }

        if (stopCheck >= size && processHLsort.isEmpty() && p.getBurst() == 0)
          endFlag = 1; // if all processes are accounted for and queue is empty we are done

        p.decBurst(); // removes 1 from burst
      }

      if (typeScheduler == 7) { // P-PL
        if (p.getBurst() == 0 ||
            !processLHsort.isEmpty() &&
                processLHsort.peek().getPri() < p.getPri()) { // if burst has ended
          EndProcess end = new EndProcess(p.getID(), start, cpuTime);
          sProcess.add(end);
          start = cpuTime;

          if (!processLHsort.isEmpty())
            p = swapPre(p);
        }

        if (stopCheck >= size && processLHsort.isEmpty() && p.getBurst() == 0) { // if all processes are accounted for
                                                                                 // and queue is empty we are done
          endFlag = 1;
        }

        p.decBurst(); // removes 1 from burst
      }

      cpuTime++; // adds to cpuTIme
    }
  }

  // updates flag file to have a value of 1
  static void updateFlagFile() {
    try {
      File flagFile = new File(
          "../../../files/core/m-008/flag-file.txt");
      FileWriter writer = new FileWriter(flagFile, false);
      writer.write("1");
      writer.close();
    } catch (IOException e) {
      System.out.println("Error in updateFlagFile()");
    }
  }

  public static void main(String args[]) throws IOException {
    inFile(); // reads from file
    size = process.size();
    CPU(); // CPU scheduler
    outFile(); // outputs to file
    updateFlagFile(); // updates flag file
  }

  public static void inFile() throws FileNotFoundException { // Good
    buffer = new StringBuffer();
    File inFile = new File(
        "../../../files/core/m-008/in-008.dat");
    Scanner in = new Scanner(inFile);

    int pID, pArrive, pBurst, pPriority, i = 0;
    String line;

    // Priority Low to High (Preemptive) scheduler type
    line = "7";

    buffer.append(line.charAt(i)); // scheduler type

    typeScheduler = Integer.parseInt(buffer.toString()); // parses it into an integer for the type of shoulder
    buffer.setLength(0); // clear buffer

    if (typeScheduler == 4) { // if it IS a RR we need to find the quantum time
      while (line.charAt(i) == ' ')
        i++; // goes through whitespace

      while (line.charAt(i) != '/') {
        buffer.append(line.charAt(i));
        i++;
      }

      quantum = Integer.parseInt(buffer.toString()); // parses it into an integer for the quantum time
      buffer.setLength(0); // clear buffer
    }

    while (in.hasNextLine()) { // goes through entire file
      i = 0;
      line = in.nextLine();/* assigns string to line */

      // Process ID------------------------//
      while (line.charAt(i) != ' ') { // copies non-whitespace characters to array
        buffer.append(line.charAt(i));
        i++;
      }

      pID = Integer.parseInt(buffer.toString()); // parses it into an integer for ID
      buffer.setLength(0); // clear buffer

      while (line.charAt(i) == ' ') { // goes through whitespace
        i++;

        if (line.charAt(i) != ' ')
          break; // if a char that isnt whitespace is detected break
      }

      // Process Arrival Time------------------------//
      while (line.charAt(i) != ' ') {
        buffer.append(line.charAt(i));
        i++;
      }

      pArrive = Integer.parseInt(buffer.toString());
      buffer.setLength(0);

      while (line.charAt(i) == ' ') {
        i++;

        if (line.charAt(i) != ' ')
          break;
      }

      // Process Burst------------------------//
      while (line.charAt(i) != ' ') {
        buffer.append(line.charAt(i));
        i++;
      }

      pBurst = Integer.parseInt(buffer.toString());
      buffer.setLength(0);

      while (line.charAt(i) == ' ')
        i++;

      // Process Priority------------------------//
      while (line.charAt(i) != '/') {
        buffer.append(line.charAt(i));
        i++;
      }

      pPriority = Integer.parseInt(buffer.toString());
      buffer.setLength(0); // clear buffer

      // Creates a new Process and puts it into process array
      Process e = new Process(pID, pArrive, pBurst, pPriority);
      process.add(e);
    }
    in.close();
  }

  public static void outFile() throws IOException {
    File outFile = new File(
        "../../../files/core/m-008/out-008.dat");
    PrintWriter out = new PrintWriter(outFile);
    String scheduler = null;

    if (typeScheduler == 2)
      scheduler = "Priority High -> Low(Non-Preemptive)";
    else if (typeScheduler == 3)
      scheduler = "Priority Low -> High(Non-Preemptive)";
    else if (typeScheduler == 6)
      scheduler = "Priority High -> Low(Preemptive)";
    else if (typeScheduler == 7)
      scheduler = "Priority Low -> High(Preemptive)";

    out.println("Type of Scheduler: " + scheduler);
    out.println("Number of Processes: " + size);

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

      // print CPU's current process
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

class EndProcess {

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
