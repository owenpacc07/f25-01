import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileWriter;
import java.io.IOException;
import java.io.PrintWriter;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Scanner;

public class m042 {
	static String mid = "042";
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
		m042 M042 = new m042();
		try {
			M042.inFile();
			// prints instance variables to ensure they are set
			System.out.println("Min = " + M042.getMin());
			System.out.println("Max = " + M042.getMax());
			System.out.println("Head = " + M042.getHead());
			System.out.println("Data = " + Arrays.toString(M042.getInData()));
			// run calculations
			M042.SSTF(M042.getInData(), M042.getHead());
			M042.outFile(); // output to file
		} catch (Exception e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} // reads from file
		updateFlagFile(); // updates flag file
	}

	public static void calculateDifference(int queue[], int head, node diff[]) {
		for (int i = 0; i < diff.length; i++)
			diff[i].distance = Math.abs(queue[i] - head);
	}

	public static int findMin(node diff[]) {
		int index = -1, minimum = Integer.MAX_VALUE;
		for (int i = 0; i < diff.length; i++) {
			if (!diff[i].accessed && minimum > diff[i].distance) {
				minimum = diff[i].distance;
				index = i;
			}
		}
		return index;
	}

	public void SSTF(int request[], int head) {
		if (request.length == 0)
			return;

		node diff[] = new node[request.length];

		for (int i = 0; i < diff.length; i++)
			diff[i] = new node();

		int seek = 0;
		int[] sequence = new int[request.length + 1];

		for (int i = 0; i < request.length; i++) {
			sequence[i] = head;
			calculateDifference(request, head, diff);
			int index = findMin(diff);
			diff[index].accessed = true;
			seek += diff[index].distance;
			head = request[index];
		}

		sequence[sequence.length - 1] = head;

		// added to remove head from output, to make more consistant with other outputs
		sequence = Arrays.copyOfRange(sequence, 1, sequence.length);

		// print the sequence
		for (int i = 0; i < sequence.length; i++)
			setOutData(getOutData() + sequence[i] + " ");
		System.out.println("Out Data: " + getOutData());
	}

	// Reads input file
	public void inFile() throws FileNotFoundException {
		ArrayList<String> arr = new ArrayList<>();
		File inFile = new File(workingDirectory + "/in-disk.dat");
		// File inFile = new File("in-" + mid + ".dat");
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

	// Writes to output file
	public void outFile() throws IOException {
		File outFile = new File(workingDirectory + "/out-" + mid + ".dat");
		// File outFile = new File("out-" + mid + ".dat");
		PrintWriter out = new PrintWriter(outFile);
		out.println(getMin() + " " + getMax());
		out.println(getHead());
		out.println(getOutData());
		out.close();
	}

	// updates flag file to have a value of 1
	static void updateFlagFile() {
		try {
			File flagFile = new File(workingDirectory + "/flag-file.txt");
			// File flagFile = new File("flag-file.txt");
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

// Java program for implementation of
// SSTF disk scheduling
class node {

	// represent difference between
	// head position and track number
	int distance = 0;

	// true if track has been accessed
	boolean accessed = false;
}
