import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileWriter;
import java.io.IOException;
import java.io.PrintWriter;
import java.util.ArrayList;
import java.util.Random;
import java.util.Scanner;

//
// 033 File Allocation 
// Indexed
//

public class m033 {
	static String mid = "033";
	static String workingDirectory = "../../../files/core/m-" + mid;

	private static int memorySize = 0;

	private static ArrayList<Integer> memoryArray = new ArrayList<>();

	private static Random random = new Random();

	public static void main(String args[]) throws IOException {
		// Parse input file
		ArrayList<FileBlock> files = inFile();

		System.out.println(files);

		// Output data
		outFile(files);

		// updates flag file
		updateFlagFile();
	}

	public static int GetNextMemoryAddress() {
		System.out.println("MemoryArray Size: " + memoryArray.size());
		int rand = random.nextInt(memoryArray.size());

		int number = memoryArray.get(rand);

		memoryArray.remove(rand);

		return number;

	}

	// Reads input file
	public static ArrayList<FileBlock> inFile() {
		ArrayList<FileBlock> files = null;
		try {
			File inFile = new File(workingDirectory + "/in-" + mid + ".dat");
			Scanner in = new Scanner(inFile);

			// First two lines are # of storage blocks and # of files.
			// Parse them
			int memSize = Integer.parseInt(in.nextLine());
			memorySize = memSize;
			int numFiles = Integer.parseInt(in.nextLine());

			// Create an array the size of size memsize with indexes 1,2,3,...memSize
			memoryArray.clear();
			for (int index = 0; index < memSize; index++) {
				memoryArray.add(index + 1);
			}

			// Process FileBlocks (id, name, size)
			files = new ArrayList<FileBlock>();

			while (in.hasNextLine()) {
				String[] line = in.nextLine().split(",");
				if (line.length > 3) {
					throw new NumberFormatException("Illegal file block arguments");
				}

				FileBlock newFile = new FileBlock(Integer.parseInt(line[0]), line[1], Integer.parseInt(line[2]));
				files.add(newFile);
			}

		} catch (IOException e) {
			System.out.println(e.getMessage());
			System.exit(1);
		} catch (NumberFormatException e) {
			System.out.println("Invalid input format." + e.getMessage());
			System.exit(1);
		}

		if (files.equals(null)) {
			return null;
		}

		files.forEach(fileBlock -> {
			// Assign fileBlock an available head address
			System.out.println("----");
			int head = GetNextMemoryAddress();

			fileBlock.setHead(head);

			for (int i = 0; i < fileBlock.getSize(); i++) {
				int nextAddress = GetNextMemoryAddress();
				fileBlock.addAddress(nextAddress);
			}
		});

		System.out.println();
		for (FileBlock f : files) {
			System.out.println(f.toString());
		}

		return files;
	}

	// Converts the data to a string and writes to output file
	public static void outFile(ArrayList<FileBlock> files) {
		try {
			PrintWriter pw = new PrintWriter(workingDirectory + "/out-" + mid + ".dat");
			pw.println(memorySize);
			for (FileBlock f : files) {
				pw.println(f.toString());
			}
			pw.close();
		} catch (FileNotFoundException e) {
			System.out.println(e.getMessage());
			System.exit(1);
		}
	}

	// updates flag file to have a value of 1
	static void updateFlagFile() {
		try {
			File flagFile = new File(workingDirectory + "/flag-file.txt");
			FileWriter writer = new FileWriter(flagFile, false);
			writer.write("1");
			writer.close();

		} catch (IOException e) {
			System.out.println(e.getMessage());
			System.exit(1);
		}
	}

}

class FileBlock {
	private int fileId, size;
	private String name;
	private int head;
	private ArrayList<Integer> data;

	public FileBlock() {
		this(1, "File", 3);
	}

	public FileBlock(int fileId, String name, int size) {
		this.fileId = fileId;
		this.name = name;
		this.size = size;
		this.data = new ArrayList<>();
	}

	public int getSize() {
		return this.size;
	}

	public void setHead(int head) {
		this.head = head;
	}

	public void addAddress(int address) {
		data.add(address);
	}

	public String toString() {
		StringBuffer buffer = new StringBuffer();

		// buffer.append(this.size + ";");

		buffer.append(String.format("%d,", this.fileId));

		buffer.append(String.format("%d,", this.head));

		for (Integer i : data) {
			buffer.append(String.format("%d,", i));
		}

		buffer.deleteCharAt(buffer.length() - 1);

		return buffer.toString();
	}

}