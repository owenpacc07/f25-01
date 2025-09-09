import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileWriter;
import java.io.IOException;
import java.io.PrintWriter;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collections;
import java.util.Scanner;
import java.util.Vector;

public class m043 {
	static String mid = "043";
	static String workingDirectory = "../../../files/core/m-" + mid;
	private int head = 0;
	private int min = 0;
	private int max = 0;
	private int[] inData = {};
	private String outData = "";

	public static void main(String[] args) {
		m043 M043 = new m043();
		try {
			M043.inFile();
			// prints instance variables to ensure they are set
			System.out.println("Min = " + M043.getMin());
			System.out.println("Max = " + M043.getMax());
			System.out.println("Head = " + M043.getHead());
			System.out.println("Data = " + Arrays.toString(M043.getInData()));
			// run calculations
			M043.CSCAN(M043.getInData(), M043.getHead());
			M043.outFile(); // output to file
		} catch (Exception e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} // reads from file
		updateFlagFile(); // updates flag file
	}

	public void CSCAN(int arr[], int head) {
		int size = 699;
		int seek = 0;
		int distance, track;

		Vector<Integer> left = new Vector<Integer>();
		Vector<Integer> right = new Vector<Integer>();
		Vector<Integer> sequence = new Vector<Integer>();

		left.add(0);
		right.add(size - 1);

		for (int i = 0; i < arr.length; i++) {
			if (arr[i] < head)
				left.add(arr[i]);
			if (arr[i] > head)
				right.add(arr[i]);
		}

		Collections.sort(left);
		Collections.sort(right);

		for (int i = 0; i < right.size(); i++) {
			track = right.get(i);
			sequence.add(track);
			distance = Math.abs(track - head);
			seek += distance;
			head = track;
		}

		head = 0;

		seek += (size - 1);

		for (int i = 0; i < left.size(); i++) {
			track = left.get(i);
			sequence.add(track);
			distance = Math.abs(track - head);
			seek += distance;
			head = track;
		}

		if (sequence.get(sequence.size()-1) == 0 && sequence.get(sequence.size() - 2 ) == size - 1 ) {
			sequence.remove(sequence.size() - 1);
			sequence.remove(sequence.size() - 1);
		}

		// print the sequence
		for (int i = 0; i < sequence.size(); i++)
			setOutData(getOutData() + sequence.get(i) + " ");
		System.out.println("Out Data: " + getOutData());
	}

	// Reads input file
	public void inFile() throws FileNotFoundException {
		ArrayList<String> arr = new ArrayList<>();
		File inFile = new File(workingDirectory + "/in-" + mid + ".dat");
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
