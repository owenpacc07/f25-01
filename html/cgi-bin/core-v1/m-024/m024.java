
//Contributors: Jaedan Smith

import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.PrintWriter;

import java.util.Arrays;
import java.util.LinkedList;
import java.util.Queue;
import java.util.Scanner;
import java.io.FileWriter;

// shows how to READ and WRITE input/ouput data
//

public class m024 {
	static String mid = "024";
	static String workingDirectory = "../../../files/core/m-" + mid;

	public static void main(String args[]) throws IOException {
		System.out.println("Running");
		inFile(); // reads from file

		updateFlagFile(); // updates flag file
		System.out.println("Done");
	}

	// Reads input file

	public static void inFile() throws FileNotFoundException {
		Scanner sc = new Scanner(new File(workingDirectory + "/in-" + mid + ".dat"));
		String[] inputStr = sc.nextLine().split(",");
		int[] input = new int[inputStr.length];
		for (int i = 0; i < inputStr.length; i++) {
			input[i] = Integer.parseInt(inputStr[i]);
		}
		sc.close();
		try {
			LFU(input); // calculates LFU algorithm, and produced output
		} catch (IOException e) {
			e.printStackTrace();
		}
	}

	// Writes to output file
	public static void LFU(int[] input) throws IOException {
		int totalFrames = 3;
		int[] ram = new int[totalFrames];
		int[] useCount = new int[totalFrames];

		Queue<Integer> queue = new LinkedList<Integer>();

		Arrays.fill(useCount, -1);
		Arrays.fill(ram, -1); // initiate to -1 to indicate empty RAM

		// Initialize output file
		PrintWriter writer = new PrintWriter(workingDirectory + "/out-" + mid + ".dat");

		// Simulate page references
		int faults = 0;
		writer.println(totalFrames); // Number of frames in RAM

		for (int i = 0; i < input.length; i++) {
			int page = input[i];
			boolean fault = true; // assume page fault to be true;
			int foundIndex = -1;

			// Search for input in frames
			for (int j = 0; j < ram.length; j++){
				if (ram[j] == page) {
					fault = false;
					foundIndex = j;
					// found, not a page fault
				}
			}

			System.out.print(String.format("Input: %s", page));

			if(fault){
				// Page fault since search was null

				// Search for frame replacement based on LFU
				// Find frame with smallest usage count
				int minIndex = 0;
				for (int j = 0; j < useCount.length; j++){
					if (useCount[minIndex] == -1) {
						// special case: empty frame, just use it, break out of loop.
						break;
					}
					if (useCount[j] < useCount[minIndex]) {
						minIndex = j;
					}
				}
				// Check if fifo needs to be used, compare frame to be replaced with all other use counts
				boolean useFifo = false;
				for (int j = 0; j < useCount.length; j++){
					if (minIndex == j) {
						continue;
					}
					if (useCount[minIndex] == -1) {
						break;
					}
					if (useCount[minIndex] == useCount[j]){
						useFifo = true;
						break; // atleast 1 other frame with same usage count, use fifo between them
					}
				}

				if (useFifo && queue.size() > 0) {
					System.out.print("queue: ");
					for (Integer val : queue){
						System.out.print(val);
					}
					System.out.print(" ");
					// Use FIFO policy instead of LFU
					int oldestFrame = queue.poll();

					// find index of oldestFrame
					for(int j = 0; j < ram.length; j++){
						if (ram[j] == oldestFrame) {
							minIndex = j;
						}
					}
				}
				else {
					// Remove old frame from queue
					if (queue.contains(ram[minIndex])) {
						queue.remove(ram[minIndex]);
					}
				}

				System.out.println(String.format("\tMinIndex: %d\tUseFifo: %s", minIndex, useFifo));

				ram[minIndex] = page;
				useCount[minIndex] = 1;

				queue.add(page); // queue holds value of frame, not index/usecount

			}
			else {
				// Not page fault, search had something
				// Increment usage count of search's found index
				useCount[foundIndex]++;
				System.out.println();

				// Move value to back of queue.
				queue.remove(page);
				queue.add(page);
			}

			// Write RAM state to output file
			writer.print(page + ",");
			for (int j = 0; j < ram.length; j++) {
				writer.print((ram[j] == -1 ? "-" : ram[j]) + ",");
			}
			writer.print((fault ? "1" : "0"));
			for (int j = 0; j < ram.length; j++) {
				writer.print("," + (ram[j] == -1 ? "-" : useCount[j]));
			}
			writer.println();
		}

		// Write number of page faults to output file
		writer.println(faults);

		// Close output file
		writer.close();
	}

	// updates flag file to have a value of 1
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