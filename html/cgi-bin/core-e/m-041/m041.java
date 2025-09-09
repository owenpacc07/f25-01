import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.PrintWriter;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Scanner;

public class m041 {
    static String mid = "041";
    static String workingDirectory = ""; // Set in main()
    private int head = 0;
    private int min = 0;
    private int max = 0;
    private int[] inData = {};
    private String outData = "";

    public static void main(String[] args) {
        if (args.length < 1) {
            System.out.println("No experiment path provided.");
            return;
        }

        workingDirectory = args[0]; // Path passed from PHP
        m041 M041 = new m041();

        try {
            M041.inFile();
            System.out.println("Min = " + M041.getMin());
            System.out.println("Max = " + M041.getMax());
            System.out.println("Head = " + M041.getHead());
            System.out.println("Data = " + Arrays.toString(M041.getInData()));

            M041.FCFS(M041.getInData(), M041.getHead());
            M041.outFile(); // write output
        } catch (Exception e) {
            e.printStackTrace();
        }

        updateFlagFile();
    }

    public void FCFS(int arr[], int head) {
        int seek = 0;
        for (int i = 0; i < arr.length; i++) {
            int distance = Math.abs(arr[i] - head);
            seek += distance;
            head = arr[i];
        }
        for (int i = 0; i < arr.length; i++) {
            setOutData(getOutData() + arr[i] + " ");
        }
        System.out.println("Out Data: " + getOutData());
    }

    public void inFile() throws FileNotFoundException {
        ArrayList<String> arr = new ArrayList<>();
        File inFile = new File(workingDirectory + "/in-disk.dat");
        Scanner in = new Scanner(inFile);
        while (in.hasNextLine()) {
            arr.add(in.nextLine());
        }
        in.close();

        for (int i = 0; i < arr.size(); i++) {
            if (i == 0) {
                String[] tempArray = arr.get(i).split(" ");
                setMin(Integer.parseInt(tempArray[0]));
                setMax(Integer.parseInt(tempArray[1]));
            } else if (i == 1)
                setHead(Integer.parseInt(arr.get(i)));
            else if (i == 2) {
                setInData(Arrays.stream(arr.get(i).split(" ")).mapToInt(Integer::parseInt).toArray());
            }
        }
    }

    public void outFile() throws IOException {
        File outFile = new File(workingDirectory + "/out-" + mid + ".dat");
        PrintWriter out = new PrintWriter(outFile);
        out.println(getMin() + " " + getMax());
        out.println(getHead());
        out.println(getOutData());
        out.close();
    }

    static void updateFlagFile() {
        try {
            File flagFile = new File(workingDirectory + "/flag-file.txt");
            PrintWriter out = new PrintWriter(flagFile);
            out.println("1");
            out.close();
        } catch (IOException e) {
            System.out.println("Error in updateFlagFile()");
        }
    }

    public int getHead() {
        return head;
    }

    public void setHead(int head) {
        this.head = head;
    }

    public int getMin() {
        return min;
    }

    public void setMin(int min) {
        this.min = min;
    }

    public int getMax() {
        return max;
    }

    public void setMax(int max) {
        this.max = max;
    }

    public int[] getInData() {
        return inData;
    }

    public void setInData(int[] inData) {
        this.inData = inData;
    }

    public String getOutData() {
        return outData;
    }

    public void setOutData(String outData) {
        this.outData = outData;
    }
}
