import java.util.Scanner;
import java.io.*;
import java.util.*;
import java.math.*;

//Optimal Page Replacement
//Contributor: Christian Collado
public class m022 {

    static String mid = "022";
    static String compare = "page";
    static String workingDirectory = "../../../files/core-c/m-" + mid;
    static String workingDirectoryCompare = "../../../files/core-c/c-" + compare;

    static ArrayList<Integer> seq = new ArrayList<>(); //size is locked to 8?
    static int[] frame;
    static int[] usedIn = new int[3];
    static int frameSize = 3;
    static int pageFault = 0; //total number of faults
    static int framecount = 3;
    static String[] strArr;
    static int count = 7;

    /* 
    // Find next occurence of key starting from array[pos].
    // Returns the difference between the indexes of array[key] and array[pos].
    // Returns -1 if never found.
    */
    private static int findNextUsageNumber(ArrayList<Integer> array, int pos, int key){
        for (int i = pos; i < array.size(); i++){
            if (array.get(i) == key){
                return i - pos;
            }
        }
        return -1;
    }

    /*
         Contributor: Manuel Reyes
        OldOptimal code was made a method to follow strucuture of previous java files.
        Had to rewrite the algorithm because the previous algorithm, OldOptimal, does not make any sense and there was no comments
        explaining the logic. No clue what those 10 if statements were for. 
        The problem with OldOptimal is that it does not insert the first 3 frames correctly and everything after that is then messed up.
        This algorithm fixes that for the current input atleast which had problems with the previous algorithm.

        The logic should be clearly defined here atleast.

        */
    public static void RunOptimal(PrintWriter outputFileWriter){
        // Print the amount of frames being used on one line.
        outputFileWriter.println(framecount);

        // -1 will indicate an empty frame. Used when printing to the output file for the first 3 steps (line 230~)
        Arrays.fill(frame, -1);

        int wasPageFault = 0;
        // loop through the page reference string input values
        for (int index = 0; index < seq.size(); index++){
            wasPageFault = 0;
            // Print current input string
            outputFileWriter.print(seq.get(index) + ",");

            // If current number of input string is less than the size of the frame count, just insert into the frame array
            if (index < framecount){
                frame[index] = seq.get(index);
                pageFault++;
                wasPageFault = 1;
            } else {
                // Current reference page string is now greater than size of the frames, time for optimal algorithm.

                // Determine the usage of each current frame.
                for (int i = 0; i < frame.length; i++){
                    usedIn[i] = findNextUsageNumber(seq, index, frame[i]);
                }

                // Check if reference string is already in frame. If not in frame, replace, else skip.
                if (!inFrame(seq.get(index))) {
                     // Find the greatest value in usedIn array. (-1 = greatest as it is never used again)
                    int indexToReplace = 0;
                    int greatest = usedIn[indexToReplace]; //assume first is greatest. compare the rest

                    // no need to compare the rest if greatest is already -1
                    if (greatest != -1) {
                        for (int i = 1; i < usedIn.length; i++){
                            if (greatest < usedIn[i]) {
                                greatest = usedIn[i];
                                indexToReplace = i;
                            }
                         }
                    }

                    // replace page frame
                    frame[indexToReplace] = seq.get(index);
                    wasPageFault = 1;
                }
            }

            // Print frames
            for(int i = 0; i < frame.length; i++){
                if (frame[i] == -1) {
                    outputFileWriter.print("-,");
                } else{
                    outputFileWriter.print(frame[i] + ",");
                }
            }

            // Print page fault
            outputFileWriter.print(wasPageFault + ",");

            // Print UsedIn Frame array values
            for(int i = 0; i < frame.length; i++){
                if (i == frame.length-1){
                    outputFileWriter.print(usedIn[i]);
                } else{
                    outputFileWriter.print(usedIn[i] + ",");
                }
            }
            outputFileWriter.println();
        }
        // print total page faults at the end
        outputFileWriter.println(pageFault);

        outputFileWriter.close();
    }

    public static void main(String[] args) throws FileNotFoundException, IOException {

        File inFile = new File(workingDirectoryCompare + "/in-" + compare + ".dat");
        File outFile = new File(workingDirectory + "/out-" + mid + ".dat");
        PrintWriter out = new PrintWriter(outFile);

        Scanner input = new Scanner(inFile);
        input.useDelimiter("\\D");

        while(input.hasNextLine()){
            seq.add(input.nextInt());
        }

        input.close();

        frame = new int[frameSize];

        /*
        System.out.print("Reference string: ");
        for (int i: seq){
            System.out.print(i);
        }
        System.out.println();
        */

        //OldOptimal(out);
        RunOptimal(out);

        updateFlagFile();
    }

    public static int indexToRemove(int currentIndex) {
        int[] temp = new int[frame.length];
        for (int i = 0; i < frame.length; i++) {
            temp[i] = frame[i];
        }

        for (int i = 0; i < temp.length; i++) {
            boolean isAlreadySet = false;
            for (int j = currentIndex + 1; j < seq.size(); j++) {
                if (temp[i] == seq.get(j)) {
                    temp[i] = j;
                    isAlreadySet = true;
                    break;
                }
            }
            if (!isAlreadySet) {
                temp[i] = seq.size();
            }
        }
        return findIndexOfFurtherNormArray(temp);
    }

    public static int findIndexOfFurtherNormArray(int[] arr) {
        int ret = 0;
        for (int i = 0; i < arr.length; i++) {
            if (arr[i] > arr[ret]) {
                ret = i;
            }
        }
        return ret;
    }

    public static int findIndexOfFurther(ArrayList<Integer> arr) {
        int ret = 0;
        for (int i = 0; i < arr.size(); i++) {
            if (arr.get(i) > arr.get(ret)) {
                ret = i;
            }
        }
        return ret;
    }

    public static boolean inFrame(int page) {
        for (int each : frame) {
            if (each == page) {
                return true;
            }
        }
        return false;
    }

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
