
import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.PrintWriter;
import java.util.ArrayList;
import java.util.Collections;
import java.util.LinkedList;
import java.util.Queue;
import java.util.Scanner;
import java.io.FileWriter;

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
public class m005 {
	private static String filesDirectory = "../../../files/core/m-005";

	private static ArrayList<Process> process = new ArrayList<Process>(); // new created
	private static ArrayList<EndProcess> sProcess = new ArrayList<EndProcess>(); // writing to file

	// Round Robin
	private static Queue<Process> processRRnoSort = new LinkedList<Process>();

	// private static StringBuffer buffer;
	public static int typeScheduler,
			cpuTime, quantum, waiting,
			stopCheck, size = 0;
	public static int endFlag = 0;

	public static void addToQueue() // adds a process to a queue if the cpuTime meets arrival time
	{
		for (int i = 0; i < process.size(); i++) {
			if (process.get(i).getArrive() == cpuTime) {
				processRRnoSort.add(process.get(i));
				stopCheck++;
			}

		}
	}

	public static Process swapPre(Process p) // swaps process with one waiting in queue (Preemptive)
	{
		if (p.getBurst() > 0) // if the process is not finished then we need to put it back in the queue
		{
			processRRnoSort.add(p);
		}

		// We need a new process to replace the one we put back in
		return processRRnoSort.remove();

	}

	public static void CPU() {
		int qCounter = 0;
		int start;
		Process p = null;
		addToQueue();
		cpuTime = 0;

		// Initial Process
		p = processRRnoSort.remove();

		start = cpuTime;

		while (endFlag != 1 || cpuTime == 10000) {
			if (stopCheck <= size && cpuTime != 0)
				addToQueue();

			if (typeScheduler == 4) // RR
			{
				if (qCounter == quantum || p.getBurst() == 0) {
					EndProcess end = new EndProcess(p.getID(), start, cpuTime);
					sProcess.add(end);

					start = cpuTime;
					qCounter = 0;

					if (!processRRnoSort.isEmpty())
						p = swapPre(p);

				}

				if (processRRnoSort.isEmpty() && p.getBurst() == 0) // if all processes are accounted for and queue is
																	// empty we are done
					endFlag = 1;

				qCounter++;
				p.decBurst();
			}
			cpuTime++; // adds to cpuTIme
		}
	}

	// updates flag file to have a value of 1
	static void updateFlagFile() throws IOException{
		try {
			File flagFile = new File(filesDirectory + "/flag-file.txt");
			FileWriter writer = new FileWriter(flagFile, false);
			writer.write("1");
			writer.close();
		} catch (IOException e) {
			System.out.println("Error in updateFlagFile()" + e.getMessage());
		}
	}

	public static void main(String args[]) throws IOException {
		inFile(); // reads from file

		size = process.size();
		CPU(); // CPU scheduler

		outFile(); // outputs to file

		updateFlagFile(); // updates flag file
	}

	/*
	 * Had to rewrite the way the previous groups implemented reading inputs for
	 * this method because the previous method inFile() did not work at all. It produced errors when trying to read the input file.
	 * 
	 * Shortened it down quite a lot and imo made it easier to read and understand.
	 * They used such a weird way of reading inputs and was too hard to read and understand for my smooth brain.
	 * - Manuel
	 */
	public static void inFile() throws FileNotFoundException {
		File inputFile = new File(filesDirectory + "/in-005.dat");
		Scanner scan = new Scanner(inputFile);

		// typescheduler seems redundant but the CPU() method needs it for whatever
		// reason, this could be removed later on
		// because this file only deals with RR algorithm, so why did they bother
		// checking it at line 84?
		typeScheduler = 4;

		// quantum time can be set here, should be read
		quantum = 3;

		// Iterate through each line of the input file
		while (scan.hasNextLine()) {
			// Split the current line into parts using blankspace as the delimiter.
			/*
			 * Format is in this order: process_id, arrival_time, burst_time, priority.
			 * Input: 1 0 7 1
			 * currentProcessInfo = ["1", "0", "7", "1"]
			 */
			String currentProcessInfo[] = scan.nextLine().split(" ");

			// Copy the string array into an int array and parse the string as ints.
			// Could honestly just use keep string array and just parse it individually when creating the new process at line 180~
			int currentProcess[] = new int[currentProcessInfo.length];
			for (int i = 0; i < currentProcess.length; i++) {
				currentProcess[i] = Integer.parseInt(currentProcessInfo[i]);
			}

			// Clean up
			currentProcessInfo = null;

			// Creates a new Process and puts it into process array
			Process newProcess = new Process(currentProcess[0], currentProcess[1], currentProcess[2],
					currentProcess[3]);
			process.add(newProcess);

			//Clean up
			currentProcess = null;
		}

		// Close the scanner when finished reading input.
		scan.close();
	}

	public static void outFile() throws IOException {
		File outFile = new File(filesDirectory + "/out-005.dat");
		PrintWriter out = new PrintWriter(outFile);
		String scheduler = null;
		scheduler = "Round Robin";

		out.println("Type of Scheduler: " + scheduler);
		out.println("Number of Processes: " + size);
		// typescheduler check for RR feels unneccessary as this file only runs Round Robin... who did this?
		if (typeScheduler == 4)
			out.println("Quantum time: " + quantum);

		out.println("");

		for (int i = 0; i < sProcess.size(); i++) // loop to write processes to file
			out.println(
					(sProcess.get(i).getID()) + "," + (sProcess.get(i).getStart()) + "," + (sProcess.get(i).getEnd()));

		out.println("");

		//Print output for detailedTable
		System.out.println("PROCESS:");
		for (Process proc: process){
			System.out.println(proc);
		}
		//Collections.sort(sProcess);		// sort in ascending order by process id cuz detailed table reads it in the order of p1,p2,p3,p4
		System.out.println("SPROCESS:");
		for (EndProcess proc: sProcess){
			System.out.println(proc);
		}
		int duration = process.stream().mapToInt(p -> p.getOriginalBurstTime()).sum();
		for (int timeStep = 0; timeStep <= duration; timeStep++){
			StringBuffer line = new StringBuffer();
			line.append(timeStep + ",");

			int currentCpuProcessID = 0;
			EndProcess currentCpuProc = null;
			for(EndProcess proc: sProcess){
				if(timeStep >= proc.getStart() && timeStep < proc.getEnd()) {
					currentCpuProcessID = proc.getID();
					currentCpuProc = proc;
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

			// print CPU current process
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

	public int compareTo(EndProcess proc){
		if (this.id > proc.id){
			return 1;
		}
		else if(this.id < proc.id){
			return -1;
		}
		else 
			return 0;
	}

	public String toString(){
		StringBuffer info = new StringBuffer();
		info.append("\nProcess ID : "+id);
		info.append("\nStart Time : "+start);
		info.append("\nEnd Time : "+end);
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
