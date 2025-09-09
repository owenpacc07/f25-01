//PROCESS ALLOCATION, BEST FIT
//March 2023, Aleks Pilmanis
// edits by Amir Marji

import java.io.*;
import java.util.ArrayList;
import java.util.Scanner;
import java.lang.Integer;

public class m012 {
	static String mid = "012";
	static String workingDirectory = "../../../files/core/m-" + mid;

	public static void main(String args[]) throws IOException {
		ArrayList<String> input = inFile(); // Read input data from a file

		// Create memory slot objects, store them in an ArrayList
		int numOfMemorySlots = Integer.parseInt(input.get(0));
		ArrayList<MemorySlot> memSlots = new ArrayList<>();

		for (int i = 1; i < (numOfMemorySlots * 2); i += 2) {
			// Create memory slots based on input data and add them to the list
			MemorySlot slot = new MemorySlot(Integer.parseInt(input.get(i)), Integer.parseInt(input.get(i + 1)));
			memSlots.add(slot);
		}

		// Create process objects, store them in an ArrayList
		int newBaseIndex = numOfMemorySlots * 2 + 2;
		int numOfProcesses = Integer.parseInt(input.get(newBaseIndex - 1));
		ArrayList<Process> processes = new ArrayList<>();

		for (int i = newBaseIndex; i < (newBaseIndex + numOfProcesses * 2); i += 2) {
			// Create process objects based on input data and add them to the list
			Process proc = new Process(Integer.parseInt(input.get(i)), Integer.parseInt(input.get(i + 1)));
			processes.add(proc);
		}

		// Iterate through processes and allocate them to memory slots based on the best
		// fit
		for (int i = 0; i < processes.size(); i++) {
			int bestFitIndex = -1;
			int fitSpace = Integer.MAX_VALUE;
			boolean foundFit = false;

			for (int x = 0; x < memSlots.size(); x++) {

				// If memory slot is big enough for the process and not already allocated
				if (memSlots.get(x).size >= processes.get(i).size && !(memSlots.get(x).allocated)) {

					// Check if this fit is better than previously checked memory slots
					if (memSlots.get(x).size - processes.get(i).size < fitSpace) {
						bestFitIndex = x;
						fitSpace = memSlots.get(x).size - processes.get(i).size;
						foundFit = true;
					}
				}
			}

			// If there is a suitable fit, allocate the process to it
			if (foundFit) {
				processes.get(i).setStart(memSlots.get(bestFitIndex).start);

				// Mark memory slot as allocated
				memSlots.get(bestFitIndex).allocated = true;
			}
		}

		// Write the output to a file
		outFile(processes);

		// Update a flag file to indicate completion
		updateFlagFile();
	}

	// Reads input data from a file and returns it as an ArrayList
	public static ArrayList<String> inFile() throws FileNotFoundException {

		// Read from the input file and store each line in an ArrayList
		ArrayList<String> arr = new ArrayList<>();
		File inFile = new File(workingDirectory + "/in-" + mid + ".dat");
		Scanner in = new Scanner(inFile);
		while (in.hasNextLine()) {
			arr.add(in.nextLine());
		}
		in.close();

		// Parse each line into words and put them in a new ArrayList
		ArrayList<String> input = new ArrayList<>();

		for (int i = 0; i < arr.size(); i++) {
			String[] temp = arr.get(i).split(" ");

			for (int x = 0; x < temp.length; x++) {
				input.add(temp[x]);
			}
		}
		return input;
	}

	// Write the output to a file
	public static void outFile(ArrayList<Process> processes) throws IOException {
		File outFile = new File(workingDirectory + "/out-" + mid + ".dat");
		PrintWriter out = new PrintWriter(outFile);

		// Iterate through processes and write the allocated ones to the output file
		for (int i = 0; i < processes.size(); i++) {

			// If the process is allocated, write its details to the output file
			if (processes.get(i).allocated) {
				out.println(processes.get(i).start + " " + processes.get(i).end + " " + processes.get(i).id);
			}
		}

		out.close();
	}

	// Update a flag file to have a value of 1 to indicate completion
	static void updateFlagFile() {
		try {
			File flagFile = new File(workingDirectory + "/flag-file.txt");
			FileWriter writer = new FileWriter(flagFile, false);
			writer.write("1");
			writer.close();

		} catch (IOException e) {
			System.out.println("Error in updateFlagFile()");
		}
	}
}

class Process {
	public int id;
	public int size;
	public int start;
	public int end;
	public boolean allocated = false;

	public Process(int id, int size) {
		this.id = id;
		this.size = size;
	}

	public void setStart(int start) {
		this.start = start;
		end = start + this.size;
		allocated = true;
	}
}

class MemorySlot {
	public int start;
	public int end;
	public int size;
	public boolean allocated = false;

	public MemorySlot(int start, int end) {
		this.start = start;
		this.end = end;
		this.size = end - start;
	}
}
