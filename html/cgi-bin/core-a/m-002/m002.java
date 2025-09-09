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
public class m002 {

	private static ArrayList<Process> process = new ArrayList<Process>(); // new created
	private static ArrayList<EndProcess> sProcess = new ArrayList<EndProcess>(); // writing to file

	// Shortest Job First
	private static PriorityQueue<Process> processSJFsort = new PriorityQueue<Process>(new Comparator<Process>() { // Preemptive

		public int compare(Process first,Process second) // compares burst times
	{return first.getBurst()-second.getBurst();}});

	private static StringBuffer buffer;
	public static int typeScheduler,
			cpuTime, quantum, waiting,
			stopCheck, size = 0;
	public static int endFlag = 0;

	public static void addToQueue() // adds a process to a queue if the cpuTime meets arrival time
	{
		for (int i = 0; i < process.size(); i++) {
			if (process.get(i).getArrive() == cpuTime) {
				processSJFsort.add(process.get(i));
				stopCheck++;
			}
		}
	}

	public static Process swap(Process p) // swaps process with one waiting in queue (Non-Preemptive)
	{
		return processSJFsort.remove();
	}

	public static Process swapPre(Process p) // swaps process with one waiting in queue (Preemptive)
	{
		if (p.getBurst() > 0) // if the process is not finished then we need to put it back in the queue
			processSJFsort.add(p);

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

			if (p.getBurst() == 0) // if burst has ended
			{
				EndProcess end = new EndProcess(p.getID(), start, cpuTime);
				sProcess.add(end);
				start = cpuTime;

				if (!processSJFsort.isEmpty())
					p = swap(p);

			}

			if (processSJFsort.isEmpty() && p.getBurst() == 0) // if all processes are accounted for and queue is empty
																// we are done
				endFlag = 1;

			p.decBurst(); // removes 1 from burst

			cpuTime++; // adds to cpuTIme
		}
	}

	// updates flag file to have a value of 1
	static void updateFlagFile() throws IOException {
		// File("/var/www/projects/s23-01/html/files/core-a/m-002/flag-file.txt");
		File flagFile = new File("../../../files/core-a/m-002/flag-file.txt");
		// File flagFile = new
		// File("/Applications/XAMPP/htdocs/vizos/html/files/core-a/m-002/flag-file.txt");
		if (flagFile.createNewFile())
			System.out.println("Created new flagfile for m002");
		if (flagFile.exists()) {
			try {
				FileWriter writer = new FileWriter(flagFile, false);
				writer.write("1");
				writer.close();

			} catch (IOException e) {
				System.out.println("Error in updateFlagFile()" + e.getMessage());
			}
		} else {
			System.out.println("flag file did not exist for m002");
		}
	}

	public static void main(String args[]) throws IOException {
		inFile(); // reads from file
		size = process.size();
		CPU(); // CPU scheduler
		outFile(); // outputs to file
		updateFlagFile(); // updates flag file
	}

	public static void inFile() throws FileNotFoundException // Good
	{
		try {
			buffer = new StringBuffer();
			File inFile = new File("../../../files/core-a/m-002/in-002.dat");
			Scanner in = new Scanner(inFile);

			int pID, pArrive, pBurst, pPriority, i = 0;
			String line;

			// SJF (non-preemptive) scheduler type
			line = "1";

			buffer.append(line.charAt(i)); // scheduler type

			typeScheduler = Integer.parseInt(buffer.toString()); // parses it into an integer for the type of shoulder
			buffer.setLength(0); // clear buffer

			while (in.hasNextLine()) // goes through entire file
			{
				i = 0;
				line = in.nextLine(); /* assigns string to line */

				// Process ID------------------------//
				while (line.charAt(i) != ' ') // copies non-whitespace characters to array
				{
					buffer.append(line.charAt(i));
					i++;
				}

				pID = Integer.parseInt(buffer.toString()); // parses it into an integer for ID
				buffer.setLength(0); // clear buffer

				while (line.charAt(i) == ' ') // goes through whitespace
				{
					i++;

					if (line.charAt(i) != ' ') // if a char that isnt whitespace is detected break
						break;
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
		} catch (FileNotFoundException e) {
			System.out.println("File not found\n" + e.getMessage());
		}
	}

	public static void outFile() throws IOException {
		File outputFile = new File("../../../files/core-a/m-002/out-002.dat");
		try {
			PrintWriter out = new PrintWriter(outputFile);
			String scheduler = null;

			scheduler = "Shortest Job First(Non-Preemptive)";

			out.println("Type of Scheduler: " + scheduler);
			out.println("Number of Processes: " + size);
			out.println("");

			for (int i = 0; i < sProcess.size(); i++) // loop to write processes to file
				out.println((sProcess.get(i).getID()) + "," + (sProcess.get(i).getStart()) + ","
						+ (sProcess.get(i).getEnd()));

			out.println("");

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
						line.append("P"+proc.getID() + ",");
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
				out.println(line);
			}
			out.close();
		} catch (Exception e) {
			System.out.println("FILE WASNT FOUND?");
		}
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

	public int getOriginalBurstTime(){
		return this.originalBurstTime;
	}

	public int decBurst() {
		return burst = burst - 1;
	}

	public int getPri() {
		return priority;
	}

}
