import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.PrintWriter;
import java.util.ArrayList;
import java.util.Scanner;
import java.io.FileWriter;


public class m033{

	// mid = Mechanism ID
	static String mid = "033";
	static String workingDirectory = "/var/www/projects/f23-05/html/files/core/m-" + mid;

	public static void main(String args[]) throws IOException
	{
		inFile();	//reads from file
		outFile();	//outputs to file 
		updateFlagFile(); // updates flag file
	}
	
	// Reads input file
	public static void inFile() throws FileNotFoundException //Good
	{ 
		ArrayList<String> arr = new ArrayList<>();
		File inFile = new File(workingDirectory + "/in-" + mid + ".dat");
		Scanner in = new Scanner(inFile);

		while(in.hasNextLine()) {
			arr.add(in.nextLine());
		}
		
		in.close();
	}

	// Writes to output file
	public static void outFile() throws IOException
	{
		File outFile = new File(workingDirectory + "/out-" + mid + ".dat");
		PrintWriter out = new PrintWriter(outFile);
		String output = "Automatically Generated";

		out.println(output);		
		
		out.close();
	}
	
	// updates flag file to have a value of 1
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
