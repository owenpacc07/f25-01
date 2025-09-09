import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileWriter;
import java.io.IOException;
import java.io.PrintWriter;
import java.util.ArrayList;
import java.util.Random;
import java.util.Scanner;

//
// 032 File Allocation 
// Linked
//

public class m032 {
	static String mid = "032";
	static String workingDirectory = "../../../files/core/m-" + mid;

	private static int memorySize = 0;

	private static ArrayList<Integer> memoryArray = new ArrayList<>();

	private static Random random = new Random();

	public static int GetNextMemoryAddress() {
		System.out.println("MemoryArray Size: " + memoryArray.size());
		int rand = random.nextInt(memoryArray.size());

		int number = memoryArray.get(rand);

		memoryArray.remove(rand);

		return number;

	}
	// static String workingDirectory = "../../html/files/core/m-" + mid;

	public static void main(String args[]) throws IOException {
		// Parse input file
		ArrayList<FileBlock> files = inFile();

		System.out.println(files);

		// Output data
		outFile(files);

		// updates flag file
		updateFlagFile();
	}

	// Reads input file
	public static ArrayList<FileBlock> inFile() {
		ArrayList<FileBlock> files = null;
		try {
			File inFile = new File(workingDirectory + "/in-" + mid + ".dat");
			try (Scanner scan = new Scanner(inFile)) {
				// First two lines contain size of memory, and # of files
				// Parse them
				int memSize = Integer.parseInt(scan.nextLine());
				memorySize = memSize;
				int numFiles = Integer.parseInt(scan.nextLine());

				// Create an array of size memsize with values 1,2,3,..memSize
				memoryArray.clear();
				for (int index = 0; index < memSize; index++) {
					memoryArray.add(index + 1);
				}

				// Process FileBlocks (id, name, size)
				files = new ArrayList<FileBlock>();

				while (scan.hasNextLine()) {
					String[] line = scan.nextLine().split(",");
					if (line.length > 3) {
						throw new NumberFormatException("Illegal file block arguments");
					}

					FileBlock newFile = new FileBlock(Integer.parseInt(line[0]), line[1], Integer.parseInt(line[2]));
					files.add(newFile);
				}
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
			// fileBlock.setHead();
			System.out.println("----");
			int head = GetNextMemoryAddress();

			fileBlock.setHead(head);

			for (int i = 0; i < fileBlock.getSize() - 1; i++) {
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

	public static void outFile(ArrayList<FileBlock> files) {
		try {
			PrintWriter writer = new PrintWriter(workingDirectory + "/out-" + mid + ".dat");

			writer.println(memorySize);
			for (FileBlock f : files) {
				writer.println(f.toString());
			}

			writer.close();
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