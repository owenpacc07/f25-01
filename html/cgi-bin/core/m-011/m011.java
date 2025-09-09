//PROCESS ALLOCATION, FIRST FIT
//March 2023, Aleks Pilmanis
// edits by Amir Marji

import java.io.*;
import java.util.ArrayList;
import java.util.Scanner;

public class m011 {
    static String mid = "011";
    static String workingDirectory = "../../../files/core/m-" + mid;

    public static void main(String args[]) throws IOException {
        ArrayList<String> input = inFile();

        // Create memory slot objects, store them in arraylist
        int numOfMemorySlots = Integer.parseInt(input.get(0));
        ArrayList<MemorySlot> memSlots = new ArrayList<>();

        for (int i = 1; i < (numOfMemorySlots * 2); i += 2) {
            MemorySlot slot = new MemorySlot(Integer.parseInt(input.get(i)), Integer.parseInt(input.get(i + 1)));
            memSlots.add(slot);
        }

        // Create process objects, store them in arraylist
        int newBaseIndex = numOfMemorySlots * 2 + 2;
        int numOfProcesses = Integer.parseInt(input.get(newBaseIndex - 1));
        ArrayList<Process> processes = new ArrayList<>();

        for (int i = newBaseIndex; i < (newBaseIndex + numOfProcesses * 2); i += 2) {
            Process proc = new Process(Integer.parseInt(input.get(i)), Integer.parseInt(input.get(i + 1)));
            processes.add(proc);
        }

        // Iterate through processes, for each process allocate through each memory slot
        // to find a fit
        for (int i = 0; i < processes.size(); i++) {
            for (int x = 0; x < memSlots.size(); x++) {
                // If memory slot is big enough for process, and neither process or memory slot
                // is allocated already
                if (memSlots.get(x).size >= processes.get(i).size &&
                        !(processes.get(i).allocated) && !(memSlots.get(x).allocated)) {
                    // Allocate process to memory slot, mark process as allocated
                    processes.get(i).setStart(memSlots.get(x).start);
                    // Mark memory slot as allocated
                    memSlots.get(x).allocated = true;
                }
            }
        }

        // Write output to file
        outFile(processes);

        // Update flag file
        updateFlagFile();
    }

    // Reads input file and returns as arraylist
    public static ArrayList<String> inFile() throws FileNotFoundException {
        // Reads from input file, stores each line in an ArrayList
        ArrayList<String> arr = new ArrayList<>();
        File inFile = new File(workingDirectory + "/in-" + mid + ".dat");
        Scanner in = new Scanner(inFile);
        while (in.hasNextLine()) {
            arr.add(in.nextLine());
        }
        in.close();

        // Parses each line into words, and puts in new ArrayList
        ArrayList<String> input = new ArrayList<>();
        for (int i = 0; i < arr.size(); i++) {
            String[] temp = arr.get(i).split(" ");
            for (int x = 0; x < temp.length; x++) {
                input.add(temp[x]);
            }
        }
        return input;
    }

    // Writes to output file
    public static void outFile(ArrayList<Process> processes) throws IOException {
        File outFile = new File(workingDirectory + "/out-" + mid + ".dat");
        PrintWriter out = new PrintWriter(outFile);

        // Iterate through processes
        for (int i = 0; i < processes.size(); i++) {
            // If process is allocated, write it to the output file
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
        end = start + this.size;
        allocated = true;
    }
}

class MemorySlot {
    public int start;
    public int end;
    public int size;
    public boolean allocated = false;

    public MemorySlot(int start, int end) {
        this.start = start;
        this.end = end;
        this.size = end - start;
    }
}
