import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.PrintWriter;
import java.util.ArrayList;
import java.util.LinkedList;
import java.util.Queue;
import java.util.Scanner;
import java.io.FileWriter;


/*


PiD, Arrival, Burst, Priority 

-Non-preemptive
1 SJF: Shortest Job First 	-Finished
2 PH: Priority High (higher number means higher priority)
3 PL: Priority Low (lower number means higher priority)



-Preemptive-
4 RR: Round Robin
5 pSJF: Preemptive Shortest Job First
6 pPH: Preemptive Priority High (higher number means higher priority)
7 pPL: Preemptive Priority Low (lower number means higher priority)
*/
public class FCFS {
	
private static ArrayList<Process> process	= new ArrayList<Process>(); //new created
private static ArrayList<EndProcess> sProcess	= new ArrayList<EndProcess>(); //writing to file

//First Come First Serve
private static Queue<Process> processFCFSsort = new LinkedList<Process>();

private static StringBuffer buffer;
public static int typeScheduler,
					cpuTime, quantum, waiting, 
					stopCheck, size = 0;
public static int endFlag= 0;

	
	
	public static void addToQueue() //adds a process to a queue if the cpuTime meets arrival time
	{
		for(int i = 0 ; i < process.size() ; i++)
		{
			if(typeScheduler == 0 && process.get(i).getArrive() == cpuTime){
				processFCFSsort.add(process.get(i));
			}
		}
	}


	public static Process swap(Process p) //swaps process with one waiting in queue (Non-Preemptive)
	{
		return processFCFSsort.remove();
	}
	
	
	public static void CPU()
	{
		int start;
		Process p = null;
		addToQueue();
		cpuTime = 0;
		
		//Initial Process
			p = processFCFSsort.remove();

		start = cpuTime;
		
			while(endFlag != 1 || cpuTime == 10000)
			{
				if(stopCheck <= size && cpuTime != 0)
					addToQueue();

				if(typeScheduler == 0){
					if(p.getBurst() == 0 )	//if burst has ended
					{
						EndProcess end = new EndProcess(p.getID(), start, cpuTime);
						sProcess.add(end);
						start = cpuTime;
						
						if(!processFCFSsort.isEmpty())
							p = swap(p);
									
					}	
					
					if(processFCFSsort.isEmpty() && p.getBurst() == 0)	//if all processes are accounted for and queue is empty we are done
						endFlag = 1;
					
					p.decBurst();		//removes 1 from burst
				}
			cpuTime++; //adds to cpuTIme
			}				
	}
	
	// updates flag file to have a value of 1
	static void updateFlagFile() {
        try {

            File flagFile = new File("/var/www/projects/s22-02/html/files/p3/flag-file.txt");
            FileWriter writer = new FileWriter(flagFile, false);
            writer.write("1");
            writer.close();
            
        } catch (IOException e) {
            System.out.println("Error in updateFlagFile()");
        }
    }

	public static void main(String args[]) throws IOException
	{
		inFile();	//reads from file
		size = process.size();	
		CPU();		//CPU scheduler
		outFile();	//outputs to file 
		updateFlagFile(); // updates flag file
	}
	
	public static void inFile() throws FileNotFoundException //Good
	{ 
		buffer = new StringBuffer();
		File inFile = new File("/var/www/projects/s22-02/html/files/p3/in.dat");
		Scanner in = new Scanner(inFile);
		
		int pID, pArrive, pBurst, pPriority , i = 0;
		String line; 
		
		
		line = in.nextLine(); /*assigns string to line */
		//Scheduler and Quantum time if it is RR
		

		buffer.append(line.charAt(i)); //scheduler type
		i++;

		typeScheduler = Integer.parseInt(buffer.toString());	// parses it into an integer for the type of shoulder				
		buffer.setLength(0);  // clear buffer
		
		if(typeScheduler == 4)	//if it IS a RR we need to find the quantum time
		{
			while(line.charAt(i) == ' ')	//goes through whitespace
				i++; 
	
		
			while(line.charAt(i) != '/')	
			{
				buffer.append(line.charAt(i));
				i++;
			}
			
				quantum = Integer.parseInt(buffer.toString());	// parses it into an integer for the quantum time			
				buffer.setLength(0);  // clear buffer
	
		}

		
			while(in.hasNextLine())	//goes through entire file
			{
				i = 0;
				line = in.nextLine(); /*assigns string to line */

			 		//Process ID------------------------//
					while(line.charAt(i) != ' ')	//copies non-whitespace characters to array
					{
						buffer.append(line.charAt(i));
						i++;
					}
			
					pID = Integer.parseInt(buffer.toString());	// parses it into an integer for ID						
					buffer.setLength(0);  // clear buffer
					
						while(line.charAt(i) == ' ')	//goes through whitespace
						{
							i++;
							
							if(line.charAt(i) != ' ')	//if a char that isnt whitespace is detected break
								break;	
						}	
					
			 		//Process Arrival Time------------------------//
						while(line.charAt(i) != ' ')	
						{
							buffer.append(line.charAt(i));
							i++;
						}
				
						pArrive = Integer.parseInt(buffer.toString());					
						buffer.setLength(0); 
						
							while(line.charAt(i) == ' ')	
							{
								i++;
								
								if(line.charAt(i) != ' ')	
									break;	
							}		
							
						//Process Burst------------------------//
						while(line.charAt(i) != ' ')
						{
							buffer.append(line.charAt(i));
							i++;
						}
					
						pBurst = Integer.parseInt(buffer.toString());					
						buffer.setLength(0);
							
						while(line.charAt(i) == ' ')	
								i++;
						
					//Process Priority------------------------//			
					while(line.charAt(i) != '/')	
					{
						
						buffer.append(line.charAt(i));
						i++;
	
					}
					
					pPriority = Integer.parseInt(buffer.toString());		
					buffer.setLength(0);  // clear buffer		
							
					
					//Creates a new Process and puts it into process array
					Process e= new Process(pID, pArrive , pBurst , pPriority);
					process.add(e);			
		}
	in.close();
	}

	
	public static void outFile() throws IOException
	{
		File outFile = new File("/var/www/projects/s22-02/html/files/p3/out.dat");
		PrintWriter out = new PrintWriter(outFile);
		String scheduler = null;	
		
		scheduler = "First Come First Serve(Non-Preemptive)";		
	
		out.println("Type of Scheduler: " + scheduler);
		out.println("Number of Processes: " + size);
		if(typeScheduler == 4)
			out.println("Quantum time: " + quantum);
		
		out.println("");
		
		for(int i=0; i < sProcess.size(); i++)	//loop to write processes to file
				out.println( (sProcess.get(i).getID()) + "," + (sProcess.get(i).getStart()) + "," + (sProcess.get(i).getEnd()) );
		
		out.println("");
		
		int nop  =process.size();
		Integer[] pwait = new Integer[nop];
		Integer[] plastend = new Integer[nop];
		for(int i=0; i < nop; i++)
		{
		  pwait[i]=0;
		  plastend[i]=0;
		}
		float wait = 0;
		int wtime = 0;
		
		for(int i=0; i < sProcess.size(); i++)
		{
		        int pid = sProcess.get(i).getID();
		        int stime = sProcess.get(i).getStart();
		        int atime = process.get(pid-1).getArrive();	
		    	out.print(pid + "," + atime );  
		    	out.print("," + plastend[pid-1] ); 
		    	out.print("," + stime ); 
		    			        
		        if (plastend[pid-1]==0)
		           {  // Calculate waiting time for FIRST time running the Process
		              wtime = stime-atime;		              
		           }  
		          else 
		           {  // Calculate waiting time for non-FIRST time running the Process
		              wtime = stime-plastend[pid-1];			               
		           }

		        //update new END time
		        plastend[pid-1]=sProcess.get(i).getEnd();  
		        
		        pwait[pid-1]=pwait[pid-1]+wtime;
		    	out.println("," + pid + "," + wtime ); 	   
			wait = wait + wtime;
	
		}
		
			out.println(wait);
			
			float avgWait = wait/process.size();
				out.println(avgWait);
				
		//System.out.println("Succesfully printed to out.txt");
		out.close();
	}
	
	
	
	
	
}

class EndProcess {

	private int id, start, end ;
	
	public EndProcess(int id,int start, int end )
	{
		this.id = id;
		this.start = start; //equivalent to wait
		this.end = end; //equivalent to wait
	}
	
	public int getID()
	{
		return id;
	}

	
	public int getStart()
	{
		return start;
	}
	
	
	public int getEnd()
	{
		return end;
	}
	
}

class Process {
	
	private int id, arrive, burst, priority;

	
	public Process(int id, int arrive, int burst, int priority)	
	{
		this.id = id;
		this.arrive=arrive;
		this.burst=burst;
		this.priority = priority;
	}
	
	
	public int getID()
	{
		return id;
	}

	
	public int getArrive()
	{
		return arrive;
	}
	
	public int getBurst()
	{
		return burst;
	}
	
	public int decBurst()
	{
		return burst = burst - 1;
	}
	
	
	public int getPri()
	{
		return priority;
	}
	
}

