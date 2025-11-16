import java.io.*;
import java.util.ArrayList;
import java.util.Scanner;
import java.lang.Integer;

public class m013 {
    static String mid = "013";
    static String workingDirectory = "../../../files/core-a/m-" + mid;

    public static void main(String args[]) throws IOException {
        // Read input data from a file and store it in an ArrayList
        ArrayList<String> input = inFile();

        // ===== Build initial FREE list from memory slots =====
        int numOfMemorySlots = Integer.parseInt(input.get(0));
        ArrayList<MemorySlot> memSlots = new ArrayList<>(); // free blocks

        for (int i = 1; i < (numOfMemorySlots * 2); i += 2) {
            MemorySlot slot = new MemorySlot(
                Integer.parseInt(input.get(i)),
                Integer.parseInt(input.get(i + 1))
            );
            memSlots.add(slot);
        }

        // ===== Build process list =====
        int newBaseIndex = numOfMemorySlots * 2 + 2;
        int numOfProcesses = Integer.parseInt(input.get(newBaseIndex - 1));
        ArrayList<Process> processes = new ArrayList<>();

        for (int i = newBaseIndex; i < (newBaseIndex + numOfProcesses * 2); i += 2) {
            Process proc = new Process(
                Integer.parseInt(input.get(i)),
                Integer.parseInt(input.get(i + 1))
            );
            processes.add(proc);
        }

        // ===== Worst-Fit with splitting (multiple allocations per original slot) =====
        for (int i = 0; i < processes.size(); i++) {
            Process p = processes.get(i);

            int worstFitIndex = -1;
            int largestSize = Integer.MIN_VALUE;

            for (int x = 0; x < memSlots.size(); x++) {
                MemorySlot free = memSlots.get(x);
                if (free.size >= p.size) {
                    // pick the LARGEST free block that can contain the process
                    if (free.size > largestSize) {
                        largestSize = free.size;
                        worstFitIndex = x;
                    }
                }
            }

            if (worstFitIndex != -1) {
                // Place process at the START of the chosen free block
                MemorySlot chosen = memSlots.get(worstFitIndex);
                p.setStart(chosen.start);

                // Split/shrink the free block (front allocation)
                if (chosen.size == p.size) {
                    // exact fit: remove this free block
                    memSlots.remove(worstFitIndex);
                } else {
                    // shrink from the front: move start forward, reduce size; end stays the same
                    chosen.start += p.size;
                    chosen.size  -= p.size;
                    // chosen.end remains unchanged
                }
            }
            // else: no fit found -> p remains unallocated
        }

        // Write the allocated processes to an output file
        outFile(processes);

        // Update a flag file to indicate successful execution
        updateFlagFile();
    }

    // Reads input data from a file and returns it as an ArrayList
    public static ArrayList<String> inFile() throws FileNotFoundException {
        ArrayList<String> arr = new ArrayList<>();
        File inFile = new File(workingDirectory + "/in-" + mid + ".dat");
        Scanner in = new Scanner(inFile);
        while (in.hasNextLine()) {
            arr.add(in.nextLine());
        }
        in.close();

        ArrayList<String> input = new ArrayList<>();
        for (int i = 0; i < arr.size(); i++) {
            String[] temp = arr.get(i).split(" ");
            for (int x = 0; x < temp.length; x++) {
                if (!temp[x].isEmpty()) input.add(temp[x]); // skip stray empties
            }
        }
        return input;
    }

    // Writes the allocated processes to an output file
    public static void outFile(ArrayList<Process> processes) throws IOException {
        File outFile = new File(workingDirectory + "/out-" + mid + ".dat");
        PrintWriter out = new PrintWriter(outFile);

        for (int i = 0; i < processes.size(); i++) {
            if (processes.get(i).allocated) {
                out.println(processes.get(i).start + " " + processes.get(i).end + " " + processes.get(i).id);
            }
        }
        out.close();
    }

    // Updates a flag file to have a value of 1, indicating successful execution
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
        this.end = start + this.size;
        this.allocated = true;
    }
}

class MemorySlot {
    public int start;
    public int end;
    public int size;

    public MemorySlot(int start, int end) {
        this.start = start;
        this.end   = end;
        this.size  = end - start;
    }
}
