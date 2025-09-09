import java.io.File;
import java.io.FileWriter;
import java.io.IOException;
import java.io.FileNotFoundException;
import java.io.PrintWriter;

import java.util.Arrays;
import java.util.LinkedList;
import java.util.Queue;
import java.util.Scanner;

//Contributors: Jaedan Smith

public class m023 {
    static String mid = "023";
    static String workingDirectory = "../../../files/core/m-" + mid;

    public static void main(String args[]) throws IOException

    {
        int input[] = inFile(); // reads from file

        try {
            lru(input); // calculates lru algorithm, and produced output
        } catch (IOException e) {
            e.printStackTrace();
        }

        updateFlagFile();
    }

    public static int[] inFile() throws FileNotFoundException {
        Scanner sc = new Scanner(new File(workingDirectory + "/in-" + mid + ".dat"));
        String[] inputStr = sc.nextLine().split(",");

        int[] input = new int[inputStr.length];

        for (int i = 0; i < inputStr.length; i++) {
            input[i] = Integer.parseInt(inputStr[i]);
        }

        sc.close();

        return input;
    }

    // Writes to output file
    public static void lru(int[] input) throws IOException {

        int totalFrames = 3; // maybe reads from input?
        int totalPageFaults = 0;

        Page[] frames = new Page[totalFrames];
        Arrays.fill(frames, new Page(-1, -1)); // initiate to -1 to indicate empty frames

        Queue<Integer> queue = new LinkedList<Integer>();

        // Initialize output file
        PrintWriter writer = new PrintWriter(workingDirectory + "/out-" + mid + ".dat");

        writer.println(totalFrames);

        // start simulating LRU algorithm using counter implementation
        // replaced page with lowest counter
        for (int i = 0; i < input.length; i++) {
            // assume to be a page fault
            boolean wasPageFault = true;

            int indexOfFrame = -1;

            // search the input string inside the frames
            for (int j = 0; j < frames.length; j++) {
                if (frames[j].getInput() == input[i]) {
                    System.out.print("Hit: " + input[i] + ", Frame #" + j + "\n");
                    wasPageFault = false;
                    indexOfFrame = j;
                    break;
                }
            }

            if (wasPageFault) {
                // Current input string is not in frame
                totalPageFaults++;
                Page newPage = new Page(input[i], i);

                // Replace using LRU or FIFO if frequency all equal

                // Find the smallest counter page
                int minIndex = 0;
                boolean useFifo = false;
                for (int j = minIndex + 1; j < frames.length; j++) {
                    if(frames[minIndex].getLastUsed() == -1) {
                        break;
                    }
                    if (frames[minIndex].getLastUsed() > frames[j].getLastUsed()) {
                        minIndex = j;
                    }
                }


                // check fifo
                for(int j = 0; j < frames.length; j++){
                    if (frames[minIndex].getLastUsed() == -1) {
                        break;
                    }
                    if(j != minIndex && frames[minIndex].getLastUsed() == frames[j].getLastUsed()){
                        useFifo = true;
                        break;
                    }
                }

                if (queue.size() > 0 && useFifo) {
                    int fifoVal = queue.poll();

                    // change minIndex to be result of FIFO
                    for (int j = 0; j < frames.length; j++) {
                        if (frames[j].getInput() == fifoVal) {
                            minIndex = j;
                            break;
                        }
                    }
                }
                else {
                    queue.remove(frames[minIndex].getInput());
                }

                frames[minIndex] = newPage;

                queue.add(input[i]);
            } else {
                // Current input string already in frame

                // Set frame counter to index;
                frames[indexOfFrame].setLastUsed(i);

                // Move to back of queue
                queue.remove(frames[indexOfFrame].getInput());
                queue.add(frames[indexOfFrame].getInput());
            }

            writer.println(String.format("%s,%s,%s,%s,%d,%s,%s,%s", input[i], frames[0].getFormattedInput(), frames[1].getFormattedInput(),
                frames[2].getFormattedInput(), wasPageFault ? 1: 0, frames[0].getFormattedLastUsed(), frames[1].getFormattedLastUsed(), frames[2].getFormattedLastUsed() ));
        }
        writer.println(totalPageFaults);
        writer.close();
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

class Page {
    private int input;
    private int lastUsed;

    public Page(int input) {
        this.input = input;
        this.lastUsed = 0;
    }

    public Page(int input, int lastUsed) {
        this.input = input;
        this.lastUsed = lastUsed;
    }

    public int getInput() {
        return this.input;
    }

    public String getFormattedInput(){
        return this.input == -1 ? "-" : String.valueOf(this.input);
    }

    public int getLastUsed() {
        return this.lastUsed;
    }

    public String getFormattedLastUsed(){
        return this.lastUsed == -1 ? "-" : String.valueOf(this.lastUsed + 1);
    }

    public void increaseLastUsed() {
        this.lastUsed++;
    }

    public void setLastUsed(int lastUsed) {
        this.lastUsed = lastUsed;
    }
}
