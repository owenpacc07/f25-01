import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.PrintWriter;
import java.io.FileWriter;
import java.util.*;

/*  Contributors: Jaedan Smith
// MFU
// shows how to READ and WRITE input/ouput data
//*/

public class m025 {
    static String mid = "025";
    static String compare = "page";
    static String workingDirectory = "../../../files/core-c/m-" + mid;
    static String workingDirectoryCompare = "../../../files/core-c/c-" + compare;
    static ArrayList<Integer> inputString;


    public static void main(String args[]) throws IOException {
        inFile(); // reads from file

        runAlgorithm();// Run algorithm

        updateFlagFile(); // updates flag file
    }

    public static void runAlgorithm(){
        // Initialize output file
        PrintWriter writer = null;
        try{
            writer = new PrintWriter(workingDirectory + "/out-" + mid + ".dat");
            // Found in MFU.java file.
            MFU mfuAlgorithm = new MFU(inputString);

            mfuAlgorithm.run(writer);

            writer.close();
        }
        catch(FileNotFoundException e){
            System.out.println(e);
        }
    }

    // Reads input file
    public static void inFile() throws FileNotFoundException {
        Scanner sc = new Scanner(new File(workingDirectoryCompare + "/in-" + compare + ".dat"));
        String[] inputStr = sc.nextLine().split(",");

        //int[] input = new int[inputStr.length];
        // Use a DataStructure to store the input string
        inputString = new ArrayList<Integer>(inputStr.length);

        // Parse the file into the data structure
        for (String s : inputStr) {
            inputString.add(Integer.parseInt(s));
        }

        /*for (int i = 0; i < inputStr.length; i++) {
            input[i] = Integer.parseInt(inputStr[i]);
        }
        */

        // Close scanner
        sc.close();

        /*try {
            MFU(input); // calculates MF algorithm, and produced output
        } catch (IOException e) {

            e.printStackTrace();
        }
        */
    }

    // This is a deprecated function
    // Writes to output file
    public static void MFU(int[] input) throws IOException {
        // Initialize RAM
        int[] ram = new int[3];
        int[] lastUse = new int[3];
        int[] useCount = new int[3];
        Arrays.fill(lastUse, -1);
        Arrays.fill(useCount, 0);
        Arrays.fill(ram, -1); // initate to -1 to indicate empty RAM

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
                    // Find the least recently used page to replace
                    replaceIndex = j;
                }

            }
            if (replaceIndex == -1) {
                // All frames have been accessed before, so choose the page with the highest
                // useCount or lastUse
                int maxIndex = 0;
                for (int j = 1; j < ram.length; j++) {
                    if (useCount[j] > useCount[maxIndex]
                            || (useCount[j] == useCount[maxIndex] && lastUse[j] > lastUse[maxIndex])) {
                        maxIndex = j;
                    }
                }
                replaceIndex = maxIndex;
            }
            if (fault) {
                // Page fault
                faults++;
                ram[replaceIndex] = page;
                lastUse[replaceIndex] = i;
                useCount[replaceIndex] = 1;
            } else {
                // Page hit
                lastUse[replaceIndex] = i;
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
            System.out.println(e.getMessage());
        }
    }

}