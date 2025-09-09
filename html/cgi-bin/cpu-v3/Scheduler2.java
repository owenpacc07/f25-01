 
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
public class Scheduler {
	
private static ArrayList<Process> process	= new ArrayList<Process>(); //new created
private static ArrayList<EndProcess> sProcess	= new ArrayList<EndProcess>(); //writing to file



//Shortest Job First
private static PriorityQueue<Process> processSJFsort	= new PriorityQueue<Process>(new Comparator<Process>() { //Preemptive
	
	public int compare(Process first, Process second) //compares burst times 
	{
		return first.getBurst() - second.getBurst();
	}
});


//Priority High to Low(High number means high priority
private static PriorityQueue<Process> processHLsort	= new PriorityQueue<Process>(new Comparator<Process>() { //Preemptive
	
	public int compare(Process first, Process second) 
	{
		return second.getPri() - first.getPri();
	}
});

//Priority Low to High(High number means low priority)
private static PriorityQueue<Process> processLHsort	= new PriorityQueue<Process>(new Comparator<Process>() {
	
	public int compare(Process first, Process second) 
	{
		return first.getPri() - second.getPri();
	}
});

//Round Robin
private static Queue<Process> processRRnoSort = new LinkedList<Process>();



private static StringBuffer buffer;
public static int typeScheduler,
					cpuTime, quantum, waiting, 
					stopCheck, size = 0;
public static int endFlag= 0;

	
	
	public static void addToQueue() //adds a process to a queue if the cpuTime meets arrival time
	{
		for(int i = 0 ; i < process.size() ; i++)
		{
			if((typeScheduler == 1 || typeScheduler == 5 ) && process.get(i).getArrive() == cpuTime)
			{
				processSJFsort.add(process.get(i));
				stopCheck++; 
			}
			
			if((typeScheduler == 2 || typeScheduler == 6) && process.get(i).getArrive() == cpuTime)
			{
				processHLsort.add(process.get(i));
				stopCheck++; 
			}
			
			if((typeScheduler == 3 || typeScheduler == 7) && process.get(i).getArrive() == cpuTime)
			{
				processLHsort.add(process.get(i));
				stopCheck++; 

			}
			
			if(typeScheduler == 4 && process.get(i).getArrive() == cpuTime)
			{
				processRRnoSort.add(process.get(i));
				stopCheck++; 
			}
		
		}
	}


	public static Process swap(Process p) //swaps process with one waiting in queue (Non-Preemptive)
	{
			if(typeScheduler == 1)				//SJF
				return processSJFsort.remove();
			else if(typeScheduler == 2)			//P-HL
				return processHLsort.remove();
			else	//typeScheduler == 3		P-LH
				return processLHsort.remove();

	}
		
	
	public static Process swapPre(Process p) //swaps process with one waiting in queue (Preemptive)
	{
		if(p.getBurst() > 0) //if the process is not finished then we need to put it back in the queue
		{
			if(typeScheduler == 4)		//RR
				processRRnoSort.add(p);
			
			else if(typeScheduler == 5)	//P-SJF
				processSJFsort.add(p);
			
			else if(typeScheduler == 6) //P-HL
				processHLsort.add(p);
			
			else	//typeScheduler == 7  P-LH
				processLHsort.add(p);
		}

		//We need a new process to replace the one we put back in
		
		if(typeScheduler == 4 ) //RR
			return processRRnoSort.remove();
		
		if(typeScheduler == 5 ) //P-SJF
			return processSJFsort.remove();
		
		if(typeScheduler == 6 ) //P-HL 
			return processHLsort.remove();
		
		else //(typeScheduler == 7 ) //P-LH
			return processLHsort.remove();
		
	}
	
	
	public static void CPU()
	{
		int qCounter = 0;
		int start;
		Process p = null;
		addToQueue();
		cpuTime = 0;
		
		//Initial Process
		if(typeScheduler == 1 || typeScheduler == 5 )			//NP-SJF //P-SJF
			p = processSJFsort.remove();
		if(typeScheduler == 2 || typeScheduler == 6 )			//NP-HL //P-HL 
			p = processHLsort.remove();
		if(typeScheduler == 3 || typeScheduler == 7 )			//NP-LH //P-LH
			p = processLHsort.remove();
		if(typeScheduler == 4 ) 		//RR
			p = processRRnoSort.remove();

		start = cpuTime;
		
			while(endFlag != 1 || cpuTime == 10000)
			{
				if(stopCheck <= size && cpuTime != 0)
					addToQueue();

				
				if(typeScheduler == 1) //NP-SJF
				{
					
					if(p.getBurst() == 0 )	//if burst has ended
					{
						EndProcess end = new EndProcess(p.getID(), start, cpuTime);
						sProcess.add(end);
						start = cpuTime;
						
						if(!processSJFsort.isEmpty())
							p = swap(p);
									
					}	
					
					if(processSJFsort.isEmpty() && p.getBurst() == 0)	//if all processes are accounted for and queue is empty we are done
						endFlag = 1;
					
					p.decBurst();		//removes 1 from burst
				}
				
				
				if(typeScheduler == 2) //NP-PH
				{
					if(p.getBurst() == 0 )	//if burst has ended
					{
						EndProcess end = new EndProcess(p.getID(), start, cpuTime);
						sProcess.add(end);
						start = cpuTime;	
						
						if(!processHLsort.isEmpty())
							p = swap(p);
						
					}	
					
					if(processHLsort.isEmpty() && p.getBurst() == 0)	//if all processes are accounted for and queue is empty we are done
						endFlag = 1;
					
					p.decBurst();		//removes 1 from burst
					
				}
				
				
				if(typeScheduler == 3) //NP-PL
				{
					if(p.getBurst() == 0 )	//if burst has ended
					{
						EndProcess end = new EndProcess(p.getID(), start, cpuTime);
						sProcess.add(end);
						start = cpuTime;
						
						if(!processLHsort.isEmpty())
							p = swap(p);
					}	
	
					if(processLHsort.isEmpty() && p.getBurst() == 0)	//if all processes are accounted for and queue is empty we are done
						endFlag = 1;
					
					p.decBurst();		//removes 1 from burst
				}
							
				if(typeScheduler == 4) //RR
				{
					if(qCounter == quantum || p.getBurst() == 0)
					{
						EndProcess end = new EndProcess(p.getID(), start, cpuTime);
						sProcess.add(end);
						
						start = cpuTime;
						qCounter = 0;
						
						if(!processRRnoSort.isEmpty())
							p = swapPre(p);

					}
					
					if(processRRnoSort.isEmpty() && p.getBurst() == 0)	//if all processes are accounted for and queue is empty we are done
						endFlag = 1;
					
					
					qCounter++;
					p.decBurst();
				}
					
					
								
				if(typeScheduler == 5) //P-SJF
				{
					if(p.getBurst() == 0 || !processSJFsort.isEmpty() && processSJFsort.peek().getBurst() < p.getBurst())	//if burst has ended
					{
						EndProcess end = new EndProcess(p.getID(), start, cpuTime);
						sProcess.add(end);
						start = cpuTime;
						
						if(!processSJFsort.isEmpty())
							p = swapPre(p);
					}	
	
					
					
					if(processSJFsort.isEmpty() && p.getBurst() == 0)	//if all processes are accounted for and queue is empty we are done
						endFlag = 1;
										
					
					p.decBurst();		//removes 1 from burst	
				}
				
				
				if(typeScheduler == 6) //P-PH
				{					
					if(p.getBurst() == 0 || !processHLsort.isEmpty() && processHLsort.peek().getPri() > p.getPri())	//if burst has ended
					{
						EndProcess end = new EndProcess(p.getID(), start, cpuTime);
						sProcess.add(end);
						start = cpuTime;
						
						if(!processHLsort.isEmpty())
							p = swapPre(p);
					}	
					
					if(stopCheck >= size && processHLsort.isEmpty() && p.getBurst() == 0)	//if all processes are accounted for and queue is empty we are done
						endFlag = 1;
					
					p.decBurst();		//removes 1 from burst
					
				}
				
				if(typeScheduler == 7) //P-PL
				{							
					if(p.getBurst() == 0 || !processLHsort.isEmpty() && processLHsort.peek().getPri() < p.getPri())	//if burst has ended
					{
						EndProcess end = new EndProcess(p.getID(), start, cpuTime);
						sProcess.add(end);
						start = cpuTime;
						
						if(!processLHsort.isEmpty())
							p = swapPre(p);
					}	
	

					
					if(stopCheck >= size && processLHsort.isEmpty() && p.getBurst() == 0)	//if all processes are accounted for and queue is empty we are done
					{
						endFlag = 1;
					}
					
					p.decBurst();		//removes 1 from burst
				}
				
	
			cpuTime++; //adds to cpuTIme
			}				
	}
		


	public static void main(String args[]) throws IOException
	{
		inFile();	//reads from file
		size = process.size();	
		CPU();		//CPU scheduler
		outFile();	//outputs to file 
	}
	
	public static void inFile() throws FileNotFoundException //Good
	{ 
		buffer = new StringBuffer();
		File inFile = new File("in.dat");
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
		File outFile = new File("out.txt");
		PrintWriter out = new PrintWriter(outFile);
		String scheduler = null;	
		
		if(typeScheduler == 1) 
			scheduler = "Shortest Job First(Non-Preemptive)";
		else if(typeScheduler == 2) 
			scheduler = "Priority High -> Low(Non-Preemptive)";
		else if(typeScheduler == 3) 
			scheduler = "Priority Low -> High(Non-Preemptive)";
		else if(typeScheduler == 4) 
			scheduler = "Round Robin";
		else if(typeScheduler == 5) 
			scheduler = "Shortest job First(Preemptive)";
		else if(typeScheduler == 6) 
			scheduler = "Priority High -> Low(Preemptive)";
		else if(typeScheduler == 7) 
			scheduler = "Priority Low -> High(Preemptive)";		
	
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
		
			out.println("Total Waiting Time: " + wait);
			
			float avgWait = wait/process.size();
				out.println("Average Waiting time: " + avgWait);
				
		System.out.println("Succesfully printed to out.txt");
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

