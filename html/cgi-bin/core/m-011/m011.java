// PROCESS ALLOCATION, FIRST FIT (with splitting / reusable leftover space)
// March 2023, Aleks Pilmanis
// Edits by Amir Marji
// Update: Allow reusing a memory slot's leftover space after an allocation.

import java.io.*;
import java.util.ArrayList;
import java.util.Scanner;

public class m011 {
    static String mid = "011";
    static String workingDirectory = "../../../files/core/m-" + mid;

    public static void main(String args[]) throws IOException {
        ArrayList<String> input = inFile();

        // Create memory slot objects, store them in ArrayList
        int numOfMemorySlots = Integer.parseInt(input.get(0));
        ArrayList<MemorySlot> memSlots = new ArrayList<>();

        for (int i = 1; i < (numOfMemorySlots * 2); i += 2) {
            MemorySlot slot = new MemorySlot(
                Integer.parseInt(input.get(i)),
                Integer.parseInt(input.get(i + 1))
            );
            memSlots.add(slot);
        }

        // Create process objects, store them in ArrayList
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

        // FIRST-FIT with splitting:
        // For each process, scan slots in order; place into the first slot that fits.
        // If exact fit -> remove the slot; if larger -> shrink the slot from the front.
        for (int p = 0; p < processes.size(); p++) {
            Process proc = processes.get(p);
            if (proc.allocated) continue;

            for (int s = 0; s < memSlots.size(); s++) {
                MemorySlot slot = memSlots.get(s);
                int leftover = slot.size - proc.size;

                if (leftover >= 0) {
                    // Allocate at the start of this slot
                    proc.setStart(slot.start);

                    if (leftover == 0) {
                        // Exact fit: remove the free hole
                        memSlots.remove(s);
                    } else {
                        // Partial fit: consume from the front
                        slot.start += proc.size;
                        slot.size = slot.end - slot.start;

                        // Defensive check (shouldn't happen if inputs are valid)
                        if (slot.size < 0) {
                            memSlots.remove(s);
                        }
                    }

                    // First-fit: stop scanning slots for this process
                    break;
                }
            }
        }

        // Write output to file
        outFile(processes);

        // Update flag file
        updateFlagFile();
    }

    // Reads input file and returns as ArrayList
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

    // Writes to output file
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

    // Updates flag file to have a value of 1
    static void updateFlagFile() {
        try {
            File flagFile = new File(workingDirectory + "/flag-file.txt");
            FileWriter writer = new FileWriter(flagFile, false);
            writer.write("1");
            writer.close();
        } catch (IOException e) {
            System.out.println("Error in updateFlagFile()" + e.getMessage());
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
