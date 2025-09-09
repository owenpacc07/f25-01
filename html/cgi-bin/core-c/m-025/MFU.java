import java.io.PrintWriter;
import java.util.ArrayList;
import java.util.Queue;
import java.util.LinkedList;

public class MFU {
	private ArrayList<Integer> pages;
	private int totalPageFaults, FRAME_SIZE;
	private Page[] frames;
	private Queue<Integer> queue;

	public MFU(ArrayList<Integer> p) {
		pages = p;
		totalPageFaults = 0;
		FRAME_SIZE = 3;
		frames = new Page[FRAME_SIZE];
		queue = new LinkedList<Integer>();

		for (int i = 0; i < FRAME_SIZE; i++) {
			frames[i] = new Page(-1, -1);
		}
	}

	public void run(PrintWriter out) {
		out.println(FRAME_SIZE);

		for (int pageIndex = 0; pageIndex < pages.size(); pageIndex++){
			int isPageFault = 0;
			// try to find current page number in frames
			boolean found = searchInFrames(pages.get(pageIndex));

			//System.out.print(String.format("Current reference string: %s, current queue head: %d\t", pages.get(pageIndex), queue.peek()));

			/*for (Integer i : queue) {
				System.out.print(String.format("%d,", i));
			}
			System.out.println();
			*/

			if(!found){
				isPageFault = 1;
				totalPageFaults++;

				// Check for highest usage count
				int highestFrequencyIndex = 0;
				for (int j = highestFrequencyIndex+1; j < frames.length; j++) {
					if (frames[highestFrequencyIndex].useCount == -1){
						break;
					}
					if (frames[j].useCount == -1) {
						highestFrequencyIndex = j;
						break;
					}
					if (frames[j].useCount > frames[highestFrequencyIndex].useCount) {
						highestFrequencyIndex = j;
					}
				}

				// Check for FIFO
				boolean useFifo = false;
				for (int j = 0; j < frames.length; j++){
					if (frames[highestFrequencyIndex].useCount == -1) {
						break;
					}
					if (highestFrequencyIndex != j && frames[highestFrequencyIndex].useCount == frames[j].useCount) {
						useFifo = true;
					}
				}

				//System.out.println("HighestFrequency BEFORE FIFO:" + highestFrequencyIndex);

				if (useFifo) {
					//System.err.println("Using fifo");

					// Replace the frame with the frame first in with equivalent use counts
					boolean foundFirstOut = false;
					while (!foundFirstOut) {
						int frameValue = queue.poll();
						int frameIndex = getIndexInFrames(frameValue);
						int frameUsage = frames[frameIndex].useCount;

						if (frameUsage == frames[highestFrequencyIndex].useCount) {
							highestFrequencyIndex = frameIndex;
							foundFirstOut = true;
						} else {
							queue.add(frameValue);
						}
					}
				}

				//System.out.println("Highest Frequency AFTER FIFO:" + highestFrequencyIndex);

				queue.remove(frames[highestFrequencyIndex].data);

				Page newPage = new Page(pages.get(pageIndex), 1);
				frames[highestFrequencyIndex] = newPage;

				queue.add(newPage.data);
			} else {
				// Update the use count of the accessed page
				int index = getIndexInFrames(pages.get(pageIndex));

				frames[index].useCount++;

				queue.remove(pages.get(pageIndex));
				queue.add(pages.get(pageIndex));
			}

			// mark as not page fault, continue
			out.print(String.format("%d,", pages.get(pageIndex)));

			for(Page p: frames){
				out.print(String.format("%s,", p.getData()));
			}

			// mark page fault or not
			out.print(String.format("%d,", isPageFault));

			for(int j = 0; j < frames.length; j++){
				if (j == frames.length - 1 ) {
					out.println(String.format("%s", frames[j].getUseCount()));

				}else{
					out.print(String.format("%s,", frames[j].getUseCount()));
				}
			}
		}

		out.println(totalPageFaults);
	}


	public double getPageFaults() {
		return totalPageFaults;
	}

	private boolean searchInFrames(int i) {
		for (Page j : frames) {
			if (j.data == i) {
				return true;
			}
		}
		return false;
	}

	private int getIndexInFrames(int i){
		for (int j = 0; j < frames.length; j++){
			if (frames[j].data == i) {
				return j;
			}
		}

		return -1;
	}


	private int findFirstOut(int data, int usageCount) {
		int firstOutValue = 0;

		while (queue.contains(data)) {
			int currentHead = queue.poll();

			if (currentHead != data) {
				queue.add(currentHead);
			}
			else {
				firstOutValue = currentHead;
			}
		}

		return firstOutValue;
	}

	class Page {
		int data;
		int useCount;

		Page(int data, int useCount) {
			this.data = data;
			this.useCount = useCount;
		}

		public String getData(){
			return data == -1 ? "-" : String.valueOf(data);
		}

		public String getUseCount(){
			return data == -1 ? "-" : String.valueOf(useCount);
		}
	}
}