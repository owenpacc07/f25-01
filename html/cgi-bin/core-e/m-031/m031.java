import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.PrintWriter;
import java.util.ArrayList;
import java.util.Scanner;
import java.io.FileWriter;

//
// 031 File Allocation 
// Contiguous
//

public class m031 {
	static String mid = "031";
	//core e directory
	static String compare = "file";
	static String workingDirectory = "../../../files/core-e/m-" + mid;
	static String workingDirectoryCompare = "../../../files/core-e/c-" + compare;
	// static String workingDirectory = "../../html/files/core/m-" + mid;

	public static void main(String args[]) throws IOException {
        if (args.length >= 1) {
            workingDirectory = args[0]; // Use the experiment folder path passed from PHP
        }
        System.out.println(workingDirectory);
        int[] slots = inFile();
        outFile(slots);
        updateFlagFile();
        System.exit(0);
    }

	// Reads input file
	public static int[] inFile() throws FileNotFoundException {
        ArrayList<String> arr = new ArrayList<>();
        try {
            File inFile = new File(workingDirectory + "/in-file.dat"); // Read in-file.dat from experiment folder
            Scanner in = new Scanner(inFile);
            while (in.hasNextLine()) {
                arr.add(in.nextLine());
            }
            in.close();
        } catch (IOException e) {
            System.exit(1);
        }

        int slotCount = Integer.parseInt(arr.get(0));
        int fileCount = Integer.parseInt(arr.get(1));

        int fileId[] = new int[fileCount];
        int fileSize[] = new int[fileCount];
        String fileName[] = new String[fileCount];

        String currentFile;
        String currentFileSplit[] = new String[3];
        int totalNumberOfBlocks = 0;

        for (int i = 2; i <= arr.size() - 1; i++) {
            int count = i - 2;
            currentFile = arr.get(i);
            currentFileSplit = currentFile.split(",");
            fileId[count] = Integer.parseInt(currentFileSplit[0]);
            fileName[count] = currentFileSplit[1];
            fileSize[count] = Integer.parseInt(currentFileSplit[2]);
            totalNumberOfBlocks += Integer.parseInt(currentFileSplit[2]);
        }

        if (slotCount < totalNumberOfBlocks) {
            slotCount = totalNumberOfBlocks;
        }

        return allocateData(slotCount, fileCount, fileId, fileSize);
    }

	// does the calculations in allocating the data
	public static int[] allocateData(int slotCount, int fileCount, int[] fileId, int[] fileSize) {
		int[] slots = new int[slotCount];
		int slotsIndex = 0;

		// allocates the files
		for (int i = 0; i < fileCount; i++) {
			for (int x = 0; x < fileSize[i]; x++) {
				slots[slotsIndex] = fileId[i];
				slotsIndex++;
			}
			slotsIndex++;
		}

		/*
		 * //prints slots
		 * for( int i = 0; i < slots.length; i++ ){
		 * System.out.print( slots[i] + " " );
		 * }
		 */

		return slots;
	}

	// Converts the data to a string and writes to output file
	public static void outFile(int[] slots) throws IOException {
        String slotsOutput = "";
        for (int i = 0; i < slots.length; i++) {
            slotsOutput += slots[i];
            if (i < slots.length - 1) {
                slotsOutput += ",";
            }
        }
        try {
            File outFile = new File(workingDirectory + "/out-" + mid + ".dat"); // Write out-031.dat in experiment folder
            PrintWriter out = new PrintWriter(outFile);
            out.print(slotsOutput);
            out.close();
        } catch (IOException e) {
            System.exit(1);
        }
    }

	// updates flag file to have a value of 1
	static void updateFlagFile() {
		try {
			File flagFile = new File(workingDirectory + "/flag-file.txt");
			FileWriter writer = new FileWriter(flagFile, false);
			writer.write("1");
			writer.close();

		} catch (IOException e) {
			System.exit(1);
		}
	}

}