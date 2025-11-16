import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.PrintWriter;
import java.util.ArrayList;
import java.util.Collections;
import java.util.Comparator;
import java.util.LinkedList;
import java.util.List;
import java.util.Queue;
import java.util.Scanner;
import java.util.stream.Collectors;
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
public class Main {

	private static ArrayList<Process> process = new ArrayList<Process>(); // new created
	private static ArrayList<EndProcess> sProcess = new ArrayList<EndProcess>(); // writing to file

	// First Come First Serve
	private static Queue<Process> processFCFSsort = new LinkedList<Process>();

	public static int typeScheduler,
			cpuTime, quantum, waiting, size = 0;
	public static int endFlag = 0;

	public static void addToQueue() // adds a process to a queue if the cpuTime meets arrival time
	{
		for (int i = 0; i < process.size(); i++) {
			if (process.get(i).getArrive() == cpuTime) {
				processFCFSsort.add(process.get(i));
			}
		}
	}

	public static Process swap(Process p) // swaps process with one waiting in queue (Non-Preemptive)
	{
		return processFCFSsort.remove();
	}

	public static void CPU() {
		cpuTime = 0;

		// sort the processes in ascending order by arrival time
		ArrayList<Process> clonedProcess = new ArrayList<Process>();
		for (Process proc: process){
			clonedProcess.add(proc);
		}
		while (processFCFSsort.size() < process.size()){
			int minArrival = process.get(0).getArrive();
			int minIndex = 0;
			for (int i = 0; i < clonedProcess.size(); i++){
				if(clonedProcess.get(i).getArrive() < minArrival){
					minArrival = clonedProcess.get(i).getArrive();
					minIndex = i;
				}
			}
			processFCFSsort.add(clonedProcess.get(minIndex));
			clonedProcess.remove(minIndex);
		}
		clonedProcess = null;

		// Process the CPU FCFS
		int time = 0;
		int fcfsSize = processFCFSsort.size();
		for(int pID = 0; pID < fcfsSize; pID++){
			Process proc = processFCFSsort.poll();
			EndProcess endProc = new EndProcess(proc.getID(), time, time+proc.getBurst());
			time += proc.getBurst();
			sProcess.add(endProc);
		}
	}

	// updates flag file to have a value of 1
	static void updateFlagFile() {
		try {
			File flagFile = new File("flag-file.txt");
			FileWriter writer = new FileWriter(flagFile, false);
			writer.write("1");
			writer.close();
		} catch (IOException e) {
			System.out.println("Error in updateFlagFile()");
		}
	}

	public static void main(String args[]) throws IOException {
		String inputFile;
		if (args.length == 0) {
			System.out.println("No arguments given, using default values");
			inputFile = "in-1.dat";
		} else {
			System.out.println("Using input file: " + args[0]);
			inputFile = args[0];
		}
		inFile(inputFile); // reads from file
		size = process.size();
		CPU(); // CPU scheduler
		outFile(); // outputs to file
		updateFlagFile(); // updates flag file
	}

	public static void inFile(String inputFile) throws FileNotFoundException // Good
	{
		File inFile = new File(inputFile);
		Scanner scan = new Scanner(inFile);

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
		scan.close();
	}

	public static void outFile() throws IOException {
		File outFile = new File("out-1.dat");
		PrintWriter out = new PrintWriter(outFile);
		String scheduler = null;

		scheduler = "First Come First Serve(Non-Preemptive)";

		out.println("Type of Scheduler: " + scheduler);
		out.println("Number of Processes: " + size);
		out.println("");

		for (int i = 0; i < sProcess.size(); i++) // loop to write processes to file
			out.println((sProcess.get(i).getID()) + "," + (sProcess.get(i).getStart()) + "," + (sProcess.get(i).getEnd()));

		out.println("");

		int nop = process.size();
		Integer[] pwait = new Integer[nop];
		Integer[] plastend = new Integer[nop];
		for (int i = 0; i < nop; i++) {
			pwait[i] = 0;
			plastend[i] = 0;
		}

		//Print output for detailedTable
		Collections.sort(sProcess);		// sort in ascending order by process id cuz detailed table reads it in the order of p1,p2,p3,p4
		int duration = process.stream().mapToInt(p -> p.getOriginalBurstTime()).sum();
		for (int timeStep = 0; timeStep <= duration; timeStep++){
			StringBuffer line = new StringBuffer();
			line.append(timeStep + ",");

			for(EndProcess proc: sProcess){
				
				int arrivalTime = process.stream().filter((p) -> p.getID() == proc.getID()).findAny().orElse(null).getArrive();

				// guard clause cuz it hasnt arrived yet in the current time step
				if (timeStep < arrivalTime) {
					line.append("-,");
					continue;
				}

				//print the remaining burst time if the timestep is within a process' start and end time
				if (timeStep >= proc.getStart() && timeStep <= proc.getEnd()){
					line.append(proc.getEnd() - timeStep);
				}
				else if(timeStep > proc.getEnd()){
					// print 0 because the timestep has already exceeded the processes end time meaning it has finished
					line.append(0);
				}
				else if(timeStep < proc.getStart()){
					//prints the total burst time of the process since it has arrived yet is not being worked on since timestep is not yet there
					line.append(proc.getEnd() - proc.getStart());
				}
				line.append(",");
			}

			//Print what process the CPU is currently handling
			for(EndProcess proc: sProcess){
				if(timeStep >= proc.getStart() && timeStep <= proc.getEnd()){
					line.append("P" + proc.getID() + ",");
					break;
				}
			}

			//Print the waiting times for each process
			for(EndProcess proc: sProcess){
				int burstTime = proc.getEnd() - proc.getStart();
				int arrivalTime = process.stream().filter((p) -> p.getID() == proc.getID()).findAny().orElse(null).getArrive();

				int waitingTime = proc.getEnd() - arrivalTime - burstTime; // totalWaitingTime
				int dtWaitTime = timeStep - arrivalTime; // waiting time at the current timeStep

				// guard clause for printing a finished process' total waiting time
				if (timeStep > proc.getStart() && timeStep <= proc.getEnd() || timeStep > proc.getEnd()){
					line.append(waitingTime + ",");
					continue;
				}

				// if it has arrived print is instantenous waiting time, if not print nothing
				if (timeStep >= arrivalTime){
					line.append(dtWaitTime + ",");
				}else{
					line.append("-" + ",");
				}
			}

			//Print the processes in queue for the current timestep
			for(EndProcess proc: sProcess){
				int arrivalTime = process.stream().filter((p) -> p.getID() == proc.getID()).findAny().orElse(null).getArrive();

				if (timeStep > proc.getStart() && timeStep <= proc.getEnd() || timeStep > proc.getEnd()){
					continue;
				}

				if (timeStep >= arrivalTime){
					line.append("P"+proc.getID()+" ");
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

	public Process(int id, int arrive, int burst, int priority) {
		this.id = id;
		this.arrive = arrive;
		this.burst = burst;
		this.originalBurstTime = burst;
		this.priority = priority;
	}

	public int getID() {
		return this.id;
	}

	public int getArrive() {
		return this.arrive;
	}

	public int getBurst() {
		return this.burst;
	}

	public int getOriginalBurstTime(){
		return this.originalBurstTime;
	}

	public int decBurst() {
		return burst = burst - 1;
	}

	public int getPri() {
		return this.priority;
	}

	public String toString(){
		StringBuffer info = new StringBuffer();
		info.append("Process ID: " + id);
		info.append("\n");
		info.append("Arrival Time: " + arrive);
		info.append("\n");
		info.append("Burst Time: " + burst);
		info.append("\n");
		info.append("Priority: " + priority);
		info.append("\n");
		return info.toString();
	}
}
