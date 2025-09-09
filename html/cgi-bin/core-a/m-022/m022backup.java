import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.PrintWriter;
import java.util.ArrayList;
import java.util.Scanner;
import java.io.FileWriter;


public class m022backup {
	
	// mid = Mechanism ID
	static String mid = "022";
	static String workingDirectory = "/var/www/projects/f22-02/html/files/core/m-" + mid;

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
		String output = "7,0,-,-,1,1 0,0,7,-,1,2 1,7,7,1,1,3 7,7,7,1,0,3";

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