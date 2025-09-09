// Ryan Arnold | Lab 2 | CS3 | 2/14/2022

public class lab2CS3 {
	public static void main(String[] args) {
		Integer a1 = 9;
		Integer a2 = 7;
		Integer a3 = -1;
		Integer a4 = -7;
		Integer a5 = 1;
		Integer a6 = 13;
		Integer a7 = 2;
		Integer a8 = 3;
		Integer a9 = 8;
		Integer a10 = -10;
		Integer a11 = 10;

		BST bst = new BST(new BNode(a1));
		bst.insert(a2);
		bst.insert(a3);
		bst.insert(a4);
		bst.insert(a5);
		bst.insert(a6);
		bst.insert(a7);
		bst.insert(a8);
		bst.insert(a9);
		bst.insert(a10);
		bst.insert(a11);

		System.out.println("---inorder Scan----should give -10 -7 -1 1 2 3 7 8 9 10 13 ----");
		bst.inorderscan();
		System.out.println();
		System.out.println("---preorder Scan---should give 9 7 -1 -7 -10 1 2 3 8 13 10 -------");
		bst.preorderscan();
		System.out.println();
		System.out.println("---postorder Scan---should give -10 -7 3 2 1 -1 8 7 10 13 9 -------");
		bst.postorderscan();
		System.out.println();
		System.out.println("Height of the tree: " + bst.height());

		System.out.println();
		System.out.print("Enter an int to search for: ");
		java.util.Scanner sc = new java.util.Scanner(System.in);
		int p = sc.nextInt();
		if (bst.search(p, bst.root)) {
			System.out.println("Found");
		} else {
			System.out.println("Not Found");
		}
	}
}

// Class of Binary Search Tree, finish the methods in this class

class BST {
	BNode root;

	public BST(BNode r) {
		root = r;
	}

	public BST() {
		root = null;
	}

	public void insert(Comparable<Integer> e) { // insert node with data e
		if (root == null)
			root = new BNode(e);
		else {
			if (root.getData().compareTo((Integer) e) > 0) {
				/* root.data is bigger, insert e into left subtree */
				insertNode(e, root);
			} else {
				/* root.data is smaller, insert e into right subtree */
				insertNode(e, root);
			}
		}
	}

	public void insertNode(Comparable<Integer> e, BNode b) {
		if (b.getData().compareTo((Integer) e) > 0) {
			if (b.left == null) {
				b.setLeft(new BNode(e));
			} else {
				insertNode(e, b.left);
			}
		} else {
			if (b.right == null) {
				b.setRight(new BNode(e));
			} else {
				insertNode(e, b.right);
			}
		}
	}

	public int height(BNode b) { // height of the tree, this has been finished for you
		if (b == null) {
			return 0;
		}
		if (b.left == null && b.right == null) {
			return 1;
		}
		int lheight = height(b.left);
		int rheight = height(b.right);
		return 1 + Math.max(lheight, rheight);
	}

	public int height() {
		return height(this.root);
	}

	public boolean search(Comparable<Integer> e, BNode b) {
		if (b == null) {
			return false;
		}
		else if (b.getData().compareTo((Integer) e) == 0){
			return true;
		}
		else if (b.getData().compareTo((Integer) e) < 0) {
			return search(e, b.getRight());
		}
		else {
			return search(e, b.getLeft());
		}
	}

	public void inorderscan(BNode b) { // in-order scan of the tree
		if (b != null) {
			inorderscan(b.getLeft());
			System.out.print(b.getData() + " ");
			inorderscan(b.getRight());
		}		
	}

	public void inorderscan() {
		this.inorderscan(this.root);
	}

	public void preorderscan(BNode b) { // pre-order scan of the tree
		if (b != null) {
			System.out.print(b.getData() + " ");
			preorderscan(b.getLeft());
			preorderscan(b.getRight());
		}
	}

	public void preorderscan() {
		this.preorderscan(this.root);
	}

	public void postorderscan(BNode b) { // post-order scan of the tree
		if (b != null) {
			postorderscan(b.getLeft());
			postorderscan(b.getRight());
			System.out.print(b.getData() + " ");
		}
	}

	public void postorderscan() {
		this.postorderscan(this.root);
	}
}

// Binary Node
class BNode {
	public Comparable<Integer> data;
	public BNode left;
	public BNode right;

	public BNode(Comparable<Integer> data) {
		this.data = data;
		this.left = null;
		this.right = null;
	}

	public BNode(Comparable<Integer> data, BNode l, BNode r) {
		this.data = data;
		left = l;
		right = r;
	}

	public void setData(Comparable<Integer> data) {
		this.data = data;
	}

	public void setLeft(BNode bL) {
		this.left = bL;
	}

	public void setRight(BNode bR) {
		this.right = bR;
	}

	public Comparable<Integer> getData() {
		return data;
	}

	public BNode getLeft() {
		return left;
	}

	public BNode getRight() {
		return right;
	}

	public String toString() {
		return this.getData().toString();
	}
}