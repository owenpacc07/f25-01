
import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.PrintWriter;
import java.util.ArrayList;
import java.util.Comparator;
import java.util.LinkedList;
import java.util.PriorityQueue;
import java.util.Queue;
import java.util.Scanner;

public class test {

	public static void main(String args[]) throws IOException // ENTRY POINT of the PROGRAM
	{
		//systime = 0;
		//inFile();	//reads from file
		//size = process.size();	
		//CPU();		//CPU scheduler
		outFile();	//outputs to file 
	}
	
	public static void outFile() throws IOException
	{
		File outFile = new File("out.txt");
		PrintWriter out = new PrintWriter(outFile);
		String scheduler = null;
					
		out.println("It's written!");

		System.out.println("Succesfully printed to out.txt");
		out.close();
	}
	
}

