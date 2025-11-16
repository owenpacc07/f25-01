// PROCESS ALLOCATION, BEST FIT
// March 2023, Aleks Pilmanis
// edits by Amir Marji
// Best-Fit updated to allow multiple processes per original slot by splitting free blocks

import java.io.*;
import java.util.ArrayList;
import java.util.Scanner;
import java.lang.Integer;

public class m012 {
    static String mid = "012";
    // CHANGED: point to core-a instead of core
    static String workingDirectory = "../../../files/core-c/m-" + mid;

    public static void main(String args[]) throws IOException {
        ArrayList<String> input = inFile(); // Read input data from a file

        // ===== Build initial FREE list from memory slots =====
        int numOfMemorySlots = Integer.parseInt(input.get(0));
        ArrayList<MemorySlot> memSlots = new ArrayList<>(); // each is a FREE block

        for (int i = 1; i < (numOfMemorySlots * 2); i += 2) {
            MemorySlot slot = new MemorySlot(Integer.parseInt(input.get(i)), Integer.parseInt(input.get(i + 1)));
            memSlots.add(slot);
        }

        // ===== Build process list =====
        int newBaseIndex = numOfMemorySlots * 2 + 2;
        int numOfProcesses = Integer.parseInt(input.get(newBaseIndex - 1));
        ArrayList<Process> processes = new ArrayList<>();

        for (int i = newBaseIndex; i < (newBaseIndex + numOfProcesses * 2); i += 2) {
            Process proc = new Process(Integer.parseInt(input.get(i)), Integer.parseInt(input.get(i + 1)));
            processes.add(proc);
        }

        // ===== Best-Fit with splitting (multiple allocations per original slot) =====
        for (int i = 0; i < processes.size(); i++) {
            Process p = processes.get(i);

            int bestFitIndex = -1;
            int bestFitLeftover = Integer.MAX_VALUE;

            for (int x = 0; x < memSlots.size(); x++) {
                MemorySlot free = memSlots.get(x);

                if (free.size >= p.size) {
                    int leftover = free.size - p.size;
                    if (leftover < bestFitLeftover) {
                        bestFitLeftover = leftover;
                        bestFitIndex = x;
                    }
                }
            }

            if (bestFitIndex != -1) {
                // place process at the START of the chosen free block
                MemorySlot chosen = memSlots.get(bestFitIndex);
                p.setStart(chosen.start);

                // split/shrink the free block
                if (chosen.size == p.size) {
                    // exact fit: remove this free block
                    memSlots.remove(bestFitIndex);
                } else {
                    // shrink from the front: move start forward, reduce size; end stays the same
                    chosen.start += p.size;
                    chosen.size -= p.size;
                    // chosen.end unchanged
                }
            }
            // if bestFitIndex == -1, p remains unallocated
        }

        // ===== Output allocated processes =====
        outFile(processes);

        // ===== Flag done =====
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
                if (!temp[x].isEmpty()) input.add(temp[x]);
            }
        }
        return input;
    }

    // Write the output to a file
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
        this.end = end;
        this.size = end - start;
    }
}
