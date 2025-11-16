import java.io.*;
import java.util.*;

public class Main {
    public static void main(String[] args) throws IOException {
        String inputFile = args.length > 0 ? args[0] : "in-001.dat"; // prefer padded like v2
        
        // Read processes
        ArrayList<Process> processes = new ArrayList<>();
        Scanner scan = new Scanner(new File(inputFile));
        
        while (scan.hasNextLine()) {
            String[] parts = scan.nextLine().trim().split("\\s+");
            if (parts.length >= 4) {
                int id = Integer.parseInt(parts[0]);
                int arrival = Integer.parseInt(parts[1]); 
                int burst = Integer.parseInt(parts[2]);
                int priority = Integer.parseInt(parts[3]);
                processes.add(new Process(id, arrival, burst, priority));
            }
        }
        scan.close();
        
        // Sort by arrival time (FCFS)
        processes.sort((a, b) -> Integer.compare(a.arrival, b.arrival));
        
        // Simulate FCFS scheduling
        int currentTime = 0;
        ArrayList<Result> results = new ArrayList<>();
        
        for (Process p : processes) {
            if (currentTime < p.arrival) {
                currentTime = p.arrival;
            }
            int startTime = currentTime;
            int endTime = currentTime + p.burst;
            results.add(new Result(p.id, startTime, endTime));
            currentTime = endTime;
        }
        
        // Write output
        PrintWriter out = new PrintWriter("out-001.dat");
        out.println("Type of Scheduler: First Come First Serve(Non-Preemptive)");
        out.println("Number of Processes: " + processes.size());
        out.println();
        
        for (Result r : results) {
            out.println(r.id + "," + r.start + "," + r.end);
        }
        out.close();
    }
}

class Process {
    int id, arrival, burst, priority;
    
    Process(int id, int arrival, int burst, int priority) {
        this.id = id;
        this.arrival = arrival; 
        this.burst = burst;
        this.priority = priority;
    }
}

class Result {
    int id, start, end;
    
    Result(int id, int start, int end) {
        this.id = id;
        this.start = start;
        this.end = end;
    }
}