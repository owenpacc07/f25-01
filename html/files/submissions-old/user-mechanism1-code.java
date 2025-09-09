// This line was editted.

import java.util.LinkedList;
import java.util.Queue;
import java.util.Stack;

public class lab1CS3 {

	public static void main(String[] args) {
		// A Queue is an interface, which means you cannot construct a Queue directly.
		Queue<Integer> q = new LinkedList<>();
		q.add(6);
		q.add(1);
		q.add(8);
		q.add(4);
		q.add(7);
		System.out.println("The original queue is: " + q);

		findMax(q);

		QueueReverse(q);

		// Two sorted stacks are generated
		Stack<Integer> A = new Stack<>();

		// push elements to top of stack
		A.push(5);
		A.push(3);
		A.push(1);
		System.out.println("Stack A: " + A);

		Stack<Integer> B = new Stack<>();

		// push elements to top of stack
		B.push(6);
		B.push(4);
		B.push(2);
		System.out.println("Stack B: " + B);

		// merge two stacks
		mergeSortedStack(A, B);

	}

	// Given a queue, write a method that will find the max element in the queue.
	// YOu may only use remove()=dequeue() or add()=enqueue(), size() etc.
	// Queue must remain intact after finding the max.
	public static void findMax(Queue<Integer> q) {
		Queue<Integer> temp = new LinkedList<>();

		// Adds all elements from q to temp.
		while (!q.isEmpty()) {
			temp.add(q.remove());
		}

		// Peeks the top element of temp to get initial max.
		// While adding elements back to q, compares them to max.
		int max = temp.peek();
		int num = max;
		while (!temp.isEmpty()) {
			num = temp.remove();
			if (num > max) {
				max = num;
			}
			q.add(num);
		}

		System.out.println("The maximum number in the queue is:" + max);
		System.out.println("The queue after finding max is: " + q);
	}

	// Suppose you want to reverse a queue in its current state – last element of
	// the queue should be made
	// the first element, etc. You can do this by dequeue-ing and pushing to a
	// Stack, till the queue is empty,
	// and then popping the stack and enqueue-ing popped elements till the stack is
	// empty. The resulting
	// queue will have its elements in the reverse order compared to the original
	// queue.
	// Write a method that will reverse a queue given to it as a parameter.
	public static void QueueReverse(Queue<Integer> q) {
		// Creates a stack to store elements.
		Stack<Integer> A = new Stack<>();

		// push all elements to top of stack
		while (!q.isEmpty()) {
			A.push(q.remove());
		}

		// pop elements from stack and adds back to queue
		while (!A.isEmpty()) {
			q.add(A.pop());
		}

		System.out.println("The reverse queue is: " + q);
	}

	// Suppose you were asked to write a method that will take two sorted stacks A
	// and B (min on top) and
	// create one stack that is sorted (min on top). You are allowed to use only the
	// stack operations such
	// as pop, push, size and peek. No other data structure such as arrays are not
	// allowed. You are allowed
	// to use stacks.
	public static void mergeSortedStack(Stack<Integer> A, Stack<Integer> B) {
		Stack<Integer> C = new Stack<>();
		Stack<Integer> D = new Stack<>();

		// push elements to top of stack by checking if the element in A or B is less than the other.
		while (!A.isEmpty() && !B.isEmpty()) {
			if (A.peek() < B.peek()) {
				D.push(A.pop());
			} else {
				D.push(B.pop());
			}
		}

		// if A is empty, push all elements from B to C.
		while (!A.isEmpty()) {
			D.push(A.pop());
		}

		// if B is empty, push all elements from A to C.
		while (!B.isEmpty()) {
			D.push(B.pop());
		}

		System.out.println("Merge Sorted Stack : " + D);
	}

}