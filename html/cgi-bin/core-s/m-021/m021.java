import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.PrintWriter;
import java.io.FileWriter;
import java.util.*;

//FIFO Repalcement 
//Contributor: Christian Collado
class m021 {
    static String mid = "021";
    static String workingDirectory = "../../../files/core-a/m-" + mid;

    static int pageFaults(ArrayList<Integer> InputValues, int a, int frames, PrintWriter out) throws FileNotFoundException {
        System.out.println(frames);
        out.println(frames);
        // Hashset will tell us if the item is set or not

        // Previous code using HashSet s did not work as intended.
        // I am more familiar with ArrayLists so I used that instead, and it is simpler to use.
        // The previous behavior was incorrect as it shifted frames up and down, but that does not happen in page replacement.
        // When arraylist.size >= frames and new input value is not in frameList
        //  Using the queue, pop the first in so it is first out.
        //  Get the index of that value in the frameList
        //  Replace that frame with the new input string
        ArrayList<Integer> frameList = new ArrayList<Integer>(frames);
        //HashSet<Object> s = new HashSet<>(frames);
        // Stores pages in FIFO order using queue
        // Queue is used to know the direction of entry for the incoming pages
        Queue<Object> queue = new LinkedList<>();

        int page_faults = 0; // total number of page faults
        int page_faults2 = 0; // marks current step as page fault

        int test1 = 0; // 0= current step has no page fault
        int test2 = 1; // 1= current step had a page fault

        for (int i = 0; i < a; i++) {
            // if set contains less than frames = 3
            if(frameList.size() < frames){
                // If item is not contained in set then add to it which is a page fault
                if(frameList.indexOf(InputValues.get(i)) == -1){
                    frameList.add(InputValues.get(i) );
                    page_faults++;
                    page_faults2 = 1;
                    queue.add(InputValues.get(i));
                }
            }
            
            /**if (s.size() < frames) {
                // If item is not contained in set then add to it which is a page fault
                if (!s.contains(InputValues[i])) {
                    s.add(InputValues[i]);
                    page_faults++;
                    page_faults2 = test2;
                    // Pushes current item to the queue
                    queue.add(InputValues[i]);

                }
            }
            */
            // When the set is filled up to 3 frames, and a new page number needs to be
            // added, page replacement occurs
            // through first in first out method, removing the first item that entered
            // before the rest
            else {
                // If input value is in frame already, no page fault, do nothing
                if (frameList.indexOf(InputValues.get(i)) != -1){
                    page_faults2 = 0;
                }
                else {
                    // Else input is not in frame; do page replacement using FIFO

                    // get value of the first out by polling it (removes and returns the top value)
                    int firstOut = (int) queue.poll();
                    //find the index of the frame being replaced
                    int frameToRemove = frameList.indexOf(firstOut);

                    // replace it with current input value
                    frameList.set(frameToRemove, InputValues.get(i));

                    // Add it to queue, not sure why previous code wrote an if statement for this... 
                    // the previous value should always be removed at this point in the code
                    queue.add(InputValues.get(i));

                    // Increment total number of page faults
                    page_faults++;
                    // Mark this frame as a page fault
                    page_faults2 = 1;
                }
                /*
                if (s.contains(InputValues[i])) {
                    page_faults2 = test1;
                }
                
                // If item is not contained in set then add to it which is a page fault
                else if (!s.contains(InputValues[i])) {
                    // first removing the first item that entered
                    int val = (int) queue.peek();

                    // removes it from the queue
                    queue.poll();
                    // queue.remove(val);

                    // Remove it from the set
                    s.remove(val);

                    // then we add the item not contained it with the space now available
                    s.add(InputValues[i]);

                    // we add the incoming item into queue to store it for knowing the order
                    if (!queue.contains(val)) {
                        queue.add(InputValues[i]);
                    }
                    page_faults++;
                    page_faults2 = test2;
                }
                */

            }

            System.out.print(InputValues.get(i) + ",");

            for (Object item : queue) {
                System.out.print(item.toString() + ",");
            }
            if (queue.size() < frames) {
                System.out.print("-,");
                // queue.add("-");
            }
            if (queue.size() < frames - 1) {
                System.out.print("-,");
                // queue.add("-");
            }

            System.out.print(page_faults2);
            System.out.println();

            out.print(InputValues.get(i) + ",");

            for (Object item : frameList) {
                out.print(item.toString() + ",");
            }
            if (queue.size() < frames) {
                out.print("-,");
                // queue.add("-");
            }
            if (queue.size() < frames - 1) {
                out.print("-,");
                // queue.add("-");
            }

            out.print(page_faults2);
            out.println();

        }
        return page_faults;
    }

    // connected successfully and running reading in input/output files
    public static void main(String args[]) throws IOException {

        File inFile = new File("../../../files/core/m-021/in-021.dat");

        File outFile = new File("../../../files/core/m-021/out-021.dat");
        PrintWriter out = new PrintWriter(outFile);

        Scanner input = new Scanner(inFile);
        input.useDelimiter("\\D");

        ArrayList<Integer> InputValues = new ArrayList<>();

        while(input.hasNextLine()){
            InputValues.add(input.nextInt());
        }

        input.close();

        int frames = 3;

        int len = InputValues.size();
        int pageFaults = pageFaults(InputValues, len, frames, out);

        out.println(pageFaults);
        System.out.println(pageFaults);

        out.close();
        updateFlagFile(); // updates flag file

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
