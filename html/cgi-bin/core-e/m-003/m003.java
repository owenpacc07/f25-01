import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.PrintWriter;
import java.util.ArrayList;
import java.util.Collections;
import java.util.Comparator;
import java.util.PriorityQueue;
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
public class m003 {

	private static ArrayList<Process> process = new ArrayList<Process>(); // new created
	private static ArrayList<EndProcess> sProcess = new ArrayList<EndProcess>(); // writing to file

	// Priority High to Low (High number means high priority)
	private static PriorityQueue<Process> processHLsort = new PriorityQueue<Process>(new Comparator<Process>() {
		public int compare(Process first, Process second) {
			return second.getPri() - first.getPri();
		}
	});

	// Priority Low to High (High number means low priority)
	private static PriorityQueue<Process> processLHsort = new PriorityQueue<Process>(new Comparator<Process>() {
		public int compare(Process first, Process second) {
			return first.getPri() - second.getPri();
		}
	});

	private static StringBuffer buffer;
	public static int typeScheduler, cpuTime, quantum, waiting, stopCheck, size = 0;
	public static int endFlag = 0;
	private static String mid = "003";
	private static String workingDirectory = "../../../files/core-e/m-" + mid; // Relative path like m001

	public static void addToQueue() // adds a process to a queue if the cpuTime meets arrival time
	{
		for (int i = 0; i < process.size(); i++) {
			if ((typeScheduler == 2 || typeScheduler == 6) && process.get(i).getArrive() == cpuTime) {
				processHLsort.add(process.get(i));
				stopCheck++;
			}

			if ((typeScheduler == 3 || typeScheduler == 7) && process.get(i).getArrive() == cpuTime) {
				processLHsort.add(process.get(i));
				stopCheck++;
			}
		}
	}

	public static Process swap(Process p) // swaps process with one waiting in queue (Non-Preemptive)
	{
		if (typeScheduler == 2) // P-HL
			return processHLsort.remove();
		else // typeScheduler == 3 P-LH
			return processLHsort.remove();
	}

	public static Process swapPre(Process p) // swaps process with one waiting in queue (Preemptive)
	{
		if (p.getBurst() > 0) // if the process is not finished then we need to put it back in the queue
		{
			if (typeScheduler == 6) // P-HL
				processHLsort.add(p);
			else // typeScheduler == 7 P-LH
				processLHsort.add(p);
		}

		// We need a new process to replace the one we put back in
		if (typeScheduler == 6) // P-HL
			return processHLsort.remove();
		else // (typeScheduler == 7 ) //P-LH
			return processLHsort.remove();
	}

	public static void CPU() {
		int start;
		Process p = null;
		addToQueue();
		cpuTime = 0;

		// Initial Process
		if (typeScheduler == 2 || typeScheduler == 6) // NP-HL //P-HL
			p = processHLsort.remove();
		if (typeScheduler == 3 || typeScheduler == 7) // NP-LH //P-LH
			p = processLHsort.remove();

		start = cpuTime;

		while (endFlag != 1 && cpuTime < 10000) {
			if (stopCheck <= size && cpuTime != 0)
				addToQueue();

			if (typeScheduler == 2) // NP-PH
			{
				if (p.getBurst() == 0) // if burst has ended
				{
					EndProcess end = new EndProcess(p.getID(), start, cpuTime);
					sProcess.add(end);
					start = cpuTime;

					if (!processHLsort.isEmpty())
						p = swap(p);
				}

				if (processHLsort.isEmpty() && p.getBurst() == 0) // if all processes are accounted for and queue is empty we are done
					endFlag = 1;

				p.decBurst(); // removes 1 from burst
			}

			if (typeScheduler == 3) // NP-PL
			{
				if (p.getBurst() == 0) // if burst has ended
				{
					EndProcess end = new EndProcess(p.getID(), start, cpuTime);
					sProcess.add(end);
					start = cpuTime;

					if (!processLHsort.isEmpty())
						p = swap(p);
				}

				if (processLHsort.isEmpty() && p.getBurst() == 0) // if all processes are accounted for and queue is empty we are done
					endFlag = 1;

				p.decBurst(); // removes 1 from burst
			}

			if (typeScheduler == 6) // P-PH
			{
				if (p.getBurst() == 0 || (!processHLsort.isEmpty() && processHLsort.peek().getPri() > p.getPri())) // if burst has ended or higher priority process arrives
				{
					EndProcess end = new EndProcess(p.getID(), start, cpuTime);
					sProcess.add(end);
					start = cpuTime;

					if (!processHLsort.isEmpty())
						p = swapPre(p);
				}

				if (stopCheck >= size && processHLsort.isEmpty() && p.getBurst() == 0) // if all processes are accounted for and queue is empty we are done
					endFlag = 1;

				p.decBurst(); // removes 1 from burst
			}

			if (typeScheduler == 7) // P-PL
			{
				if (p.getBurst() == 0 || (!processLHsort.isEmpty() && processLHsort.peek().getPri() < p.getPri())) // if burst has ended or lower priority process arrives
				{
					EndProcess end = new EndProcess(p.getID(), start, cpuTime);
					sProcess.add(end);
					start = cpuTime;

					if (!processLHsort.isEmpty())
						p = swapPre(p);
				}

				if (stopCheck >= size && processLHsort.isEmpty() && p.getBurst() == 0) // if all processes are accounted for and queue is empty we are done
					endFlag = 1;

				p.decBurst(); // removes 1 from burst
			}

			cpuTime++; // adds to cpuTime
		}
	}

	// updates flag file to have a value of 1
	static void updateFlagFile() {
		try {
			File flagFile = new File(workingDirectory + "/flag-file.txt"); // Updated to use workingDirectory
			FileWriter writer = new FileWriter(flagFile, false);
			writer.write("1");
			writer.close();
			System.out.println("Flag file updated successfully");
		} catch (IOException e) {
			System.err.println("Error in updateFlagFile(): " + e.getMessage());
		}
	}

	public static void main(String args[]) throws IOException {
		String inputFile;
		if (args.length >= 1) {
			workingDirectory = args[0]; // Use the experiment folder path passed from PHP
			System.out.println("Using experiment directory: " + workingDirectory);
			inputFile = workingDirectory + "/in-cpu.dat";
		} else {
			System.out.println("No experiment directory provided, using default: " + workingDirectory);
			inputFile = workingDirectory + "/in-cpu.dat";
		}
		inFile(inputFile); // reads from file
		size = process.size();
		CPU(); // CPU scheduler
		outFile(); // outputs to file
		updateFlagFile(); // updates flag file
	}

	public static void inFile(String inputFile) throws FileNotFoundException {
		File inFile = new File(inputFile);
		Scanner scan = new Scanner(inFile);

		// Hardcode scheduler type for Priority High (m-003 always uses type 2)
		typeScheduler = 2;

		// Iterate through all lines of the input file
		while (scan.hasNextLine()) {
			// Split the current line into parts using comma as the delimiter
			String currentProcessInfo[] = scan.nextLine().split(",");

			// Parse the string array into integers
			int currentProcess[] = new int[currentProcessInfo.length];
			for (int i = 0; i < currentProcess.length; i++) {
				currentProcess[i] = Integer.parseInt(currentProcessInfo[i].trim());
			}

			// Clean up
			currentProcessInfo = null;

			// Creates a new Process and puts it into process array
			Process newProcess = new Process(currentProcess[0], currentProcess[1], currentProcess[2],
					currentProcess[3]);
			process.add(newProcess);

			// Clean up
			currentProcess = null;
		}
		scan.close();
	}

	public static void outFile() throws IOException {
		File outFile = new File(workingDirectory + "/out-" + mid + ".dat"); // Uses workingDirectory and mid
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

		for (int i = 0; i < sProcess.size(); i++) // loop to write processes to file
			out.println(
					(sProcess.get(i).getID()) + "," + (sProcess.get(i).getStart()) + "," + (sProcess.get(i).getEnd()));

		out.println("");

		// Print output for detailedTable
		Collections.sort(sProcess); // sort in ascending order by process id
		int duration = process.stream().mapToInt(p -> p.getOriginalBurstTime()).sum();
		for (int timeStep = 0; timeStep <= duration; timeStep++) {
			StringBuffer line = new StringBuffer();
			line.append(timeStep + ",");

			for (EndProcess proc : sProcess) {
				int arrivalTime = process.stream().filter((p) -> p.getID() == proc.getID()).findAny().orElse(null)
						.getArrive();

				// guard clause cuz it hasnt arrived yet in the current time step
				if (timeStep < arrivalTime) {
					line.append("-,");
					continue;
				}

				// print the remaining burst time if the timestep is within a process' start and end time
				if (timeStep >= proc.getStart() && timeStep <= proc.getEnd()) {
					line.append(proc.getEnd() - timeStep);
				} else if (timeStep > proc.getEnd()) {
					// print 0 because the timestep has already exceeded the processes end time
					line.append(0);
				} else if (timeStep < proc.getStart()) {
					// prints the total burst time of the process since it has arrived yet is not being worked on
					line.append(proc.getEnd() - proc.getStart());
				}
				line.append(",");
			}

			// Print what process the CPU is currently handling
			for (EndProcess proc : sProcess) {
				if (timeStep >= proc.getStart() && timeStep <= proc.getEnd()) {
					line.append("P" + proc.getID() + ",");
					break;
				}
			}

			// Print the waiting times for each process
			for (EndProcess proc : sProcess) {
				int burstTime = proc.getEnd() - proc.getStart();
				int arrivalTime = process.stream().filter((p) -> p.getID() == proc.getID()).findAny().orElse(null)
						.getArrive();

				int waitingTime = proc.getEnd() - arrivalTime - burstTime; // totalWaitingTime
				int dtWaitTime = timeStep - arrivalTime; // waiting time at the current timeStep

				// guard clause for printing a finished process' total waiting time
				if (timeStep > proc.getStart() && timeStep <= proc.getEnd() || timeStep > proc.getEnd()) {
					line.append(waitingTime + ",");
					continue;
				}

				// if it has arrived print its instantaneous waiting time, if not print nothing
				if (timeStep >= arrivalTime) {
					line.append(dtWaitTime + ",");
				} else {
					line.append("-" + ",");
				}
			}

			// Print the processes in queue for the current timestep
			for (EndProcess proc : sProcess) {
				int arrivalTime = process.stream().filter((p) -> p.getID() == proc.getID()).findAny().orElse(null)
						.getArrive();

				if (timeStep > proc.getStart() && timeStep <= proc.getEnd() || timeStep > proc.getEnd()) {
					continue;
				}

				if (timeStep >= arrivalTime) {
					line.append("P" + proc.getID() + " ");
				}
			}
			out.println(line);
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

	public Process(int id, int arrive, int burst, int priority) {
		this.id = id;
		this.arrive = arrive;
		this.burst = burst;
		this.originalBurstTime = burst;
		this.priority = priority;
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

	public int getOriginalBurstTime() {
		return this.originalBurstTime;
	}

	public int decBurst() {
		return burst = burst - 1;
	}

	public int getPri() {
		return priority;
	}
}