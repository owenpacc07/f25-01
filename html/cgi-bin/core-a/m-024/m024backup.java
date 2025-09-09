
//Contributors: Jaedan Smith

import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.PrintWriter;

import java.util.Arrays;
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

		int[] ram = new int[3];
		int[] useCount = new int[3];
		Arrays.fill(useCount, 0);
		Arrays.fill(ram, -1); // initiate to -1 to indicate empty RAM

		// Initialize output file
		PrintWriter writer = new PrintWriter(workingDirectory + "/out-" + mid + ".dat");

		// Simulate page references
		int faults = 0;
		writer.println(3); // Number of frames in RAM
		
		for (int i = 0; i < input.length; i++) {
			int page = input[i];
			boolean fault = true;
			int replaceIndex = -1;

			for (int j = 0; j < ram.length; j++) {
				if (ram[j] == page) {
					// Page hit
					fault = false;
					useCount[j]++;
					break;
				} else if (ram[j] == -1) {
					// Empty frame
					fault = true;
					replaceIndex = j;
					break;
				} else if (replaceIndex == -1 || useCount[j] < useCount[replaceIndex]) {
					// Find the least frequently used page to replace
					replaceIndex = j;
				}
			}

			if (replaceIndex == -1) {
				// All frames have been accessed before, so choose the least used page to
				// replace
				int minIndex = 0;
				for (int j = 1; j < ram.length; j++) {
					if (useCount[j] < useCount[minIndex]) {
						minIndex = j;
					}
				}
				replaceIndex = minIndex;
			}

			if (fault) {
				// Page fault
				faults++;
				ram[replaceIndex] = page;
				useCount[replaceIndex] = 1;
			} else {
				// Page hit
				useCount[replaceIndex]++;
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