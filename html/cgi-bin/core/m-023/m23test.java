import java.util.*;
import java.lang.*;
import java.io.*;

public class Main {
    public static void main(String[] args) {
        int[] pages = {3, 1, 2, 1, 6, 5, 1, 3};
        int numFrames = 3;
        int[] frames = new int[numFrames];
        int[] faults = new int[pages.length];
        Arrays.fill(frames, -1);
        int numFaults = 0;

        System.out.println(numFrames);

        for (int i = 0; i < pages.length; i++) {
            int page = pages[i];
            boolean found = false;
            int j = 0;

            for (j = 0; j < numFrames; j++) {
                if (frames[j] == page) {
                    found = true;
                    break;
                }
            }

            if (!found) {
                int lruIndex = -1;
                for (j = 0; j < numFrames; j++) {
                    if (frames[j] == -1) {
                        lruIndex = j;
                        break;
                    }
                    if (lruIndex == -1 || faults[frames[j]] < faults[frames[lruIndex]]) {
                        lruIndex = j;
                    }
                }
                frames[lruIndex] = page;
                numFaults++;
            }

            faults[page] = i;
            System.out.print(page + ",");
            for (j = 0; j < numFrames; j++) {
                if (frames[j] == -1) {
                    System.out.print("-");
                } else {
                    System.out.print(frames[j]);
                }
                if (j != numFrames - 1) {
                    System.out.print(",");
                }
            }
            if (!found) {
                System.out.print(",1,");
            } else {
                System.out.print(",0,");
            }
            for (j = 0; j < numFrames; j++) {
                if (frames[j] == -1) {
                    System.out.print("-");
                } else {
                    System.out.print(frames[j]);
                }
                if (j != numFrames - 1) {
                    System.out.print(",");
                }
            }
            System.out.println();
        }

        System.out.println(numFaults);
    }
}
import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.PrintWriter;
import java.util.ArrayList;
import java.util.Scanner;
import java.io.FileWriter;

//
// THIS IS A TEMPLATE
// shows how to READ and WRITE input/ouput data
//

public class m023 {
	static String mid = "023"; 
	static String workingDirectory = "/var/www/projects/s23-01/html/files/core/m-" + mid;

	public static void main(String args[]) throws IOException
	{
		inFile(); // reads from file
		// mechanism implementation function will go eventually go here
		outFile(); // output to file
		
		updateFlagFile(); // updates flag file
	}
	
	// Reads input file
	/***** NOTE: the specific implementation will depend on the format of os mechanism's input file ******/
	public static void inFile() throws FileNotFoundException 
	{ 
		ArrayList<Integer> arr = new ArrayList<>();
		File inFile = new File(workingDirectory + "/in-" + mid + ".dat");
		Scanner in = new Scanner(inFile);
		while(in.hasNextInt()) {
			int i = in.nextInt();
			System.out.println(i);
			arr.add(in.nextInt());
		}
		in.close();
        // testing to make sure it reads in output appropriately
		File testFile = new File(workingDirectory + "/in-" + mid + "-test.dat");
		PrintWriter out = new PrintWriter(testFile);
		String testString = "";
		for (int i = 0; i < arr.size(); i++)
			testString += arr.get(i);
		out.println(testString);
		out.close();
	}

	// Writes to output file
	public static void outFile() throws IOException
	{
		File outFile = new File(workingDirectory + "/out-" + mid + ".dat");
		PrintWriter out = new PrintWriter(outFile);
		out.println("3");
		out.println("3,3,-,-,1,-,-,-");
		out.println("1,3,1,-,1,0,-,-");
		out.println("2,3,1,2,1,1,0,-");
		out.println("1,3,1,2,0,2,1,0");
		out.println("6,6,1,2,1,3,2,1");
		out.println("5,6,1,5,1,0,1,2");
		out.println("1,6,1,5,0,1,2,0");
		out.println("3,3,1,5,1,2,0,1");
		out.println("6");
		out.close();
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
