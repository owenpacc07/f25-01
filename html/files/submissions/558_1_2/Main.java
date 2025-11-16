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

public class Main {

	private static ArrayList<Process> process = new ArrayList<Process>();
	private static ArrayList<EndProcess> sProcess = new ArrayList<EndProcess>();
	private static Queue<Process> processFCFSsort = new LinkedList<Process>();

	public static int typeScheduler, cpuTime, quantum, waiting, size = 0;
	public static int endFlag = 0;

	public static void CPU() {
		cpuTime = 0;
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

		int time = 0;
		int fcfsSize = processFCFSsort.size();
		for(int pID = 0; pID < fcfsSize; pID++){
			Process proc = processFCFSsort.poll();
			EndProcess endProc = new EndProcess(proc.getID(), time, time+proc.getBurst());
			time += proc.getBurst();
			sProcess.add(endProc);
		}
	}

	public static void main(String args[]) throws IOException {
		String inputFile = args.length > 0 ? args[0] : "in-1.dat";
		
		File inFileCheck = new File(inputFile);
		if (!inFileCheck.exists()) {
			System.out.println("ERROR: Input file does not exist: " + inputFile);
			return;
		}
		
		System.out.println("Input file exists. Size: " + inFileCheck.length() + " bytes");
		
		inFile(inputFile);
		size = process.size();
		System.out.println("Parsed " + size + " processes");
		
		if (size > 0) {
			CPU();
		}
		outFile();
		updateFlagFile();
	}

	public static void inFile(String inputFile) throws FileNotFoundException {
		File inFile = new File(inputFile);
		Scanner scan = new Scanner(inFile);

		while (scan.hasNextLine()) {
			String line = scan.nextLine().trim();
			System.out.println("Reading line: '" + line + "'");
			
			if (line.isEmpty()) {
				continue;
			}
			
			String[] parts = line.split("\\s+");
			if (parts.length >= 4) {
				try {
					int id = Integer.parseInt(parts[0]);
					int arrive = Integer.parseInt(parts[1]);
					int burst = Integer.parseInt(parts[2]);
					int priority = Integer.parseInt(parts[3]);
					
					Process newProcess = new Process(id, arrive, burst, priority);
					process.add(newProcess);
					System.out.println("Added process: " + id + " " + arrive + " " + burst + " " + priority);
				} catch (NumberFormatException e) {
					System.out.println("Error parsing: " + line);
				}
			}
		}
		scan.close();
		System.out.println("Total processes loaded: " + process.size());
	}

	public static void outFile() throws IOException {
		File outFile = new File("out-1.dat");
		PrintWriter out = new PrintWriter(outFile);
		
		out.println("Type of Scheduler: First Come First Serve(Non-Preemptive)");
		out.println("Number of Processes: " + size);
		out.println("");

		for (int i = 0; i < sProcess.size(); i++) {
			EndProcess ep = sProcess.get(i);
			out.println(ep.getID() + "," + ep.getStart() + "," + ep.getEnd());
		}

		out.close();
		System.out.println("Output written to out-1.dat");
	}

	static void updateFlagFile() {
		try {
			FileWriter writer = new FileWriter("flag-file.txt", false);
			writer.write("1");
			writer.close();
		} catch (IOException e) {
			System.out.println("Error in updateFlagFile()");
		}
	}
}

class EndProcess implements Comparable<EndProcess> {
	private int id, start, end;

	public EndProcess(int id, int start, int end) {
		this.id = id;
		this.start = start;
		this.end = end;
	}

	public int getID() { return id; }
	public int getStart() { return start; }
	public int getEnd() { return end; }

	public int compareTo(EndProcess proc){
		return Integer.compare(this.id, proc.id);
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

	public int getID() { return this.id; }
	public int getArrive() { return this.arrive; }
	public int getBurst() { return this.burst; }
	public int getOriginalBurstTime() { return this.originalBurstTime; }
	public int getPri() { return this.priority; }
}