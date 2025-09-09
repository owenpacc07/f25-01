import java.util.HashMap;
import java.util.LinkedHashSet;
import java.util.LinkedList;
import java.util.List;
import java.util.Map;
import java.util.Queue;
import java.util.Set;
import java.util.Stack;

public class Lab5CS3 {

  public static void main(String[] args) throws Exception {
    /** create an undirected graph */
    Graph<Character> g = new Graph<>(false);
    /** Add some edges */
    g.addEdge('0', '1');
    g.addEdge('0', '3');
    g.addEdge('0', '5');

    g.addEdge('1', '0');
    g.addEdge('1', '2');

    g.addEdge('2', '1');
    g.addEdge('2', '6');

    g.addEdge('3', '0');
    g.addEdge('3', '4');

    g.addEdge('4', '3');
    g.addEdge('4', '8');

    g.addEdge('5', '0');
    g.addEdge('5', '8');
    g.addEdge('5', '6');

    g.addEdge('6', '2');
    g.addEdge('6', '5');
    g.addEdge('6', '9');

    g.addEdge('7', '9');

    g.addEdge('8', '4');
    g.addEdge('8', '5');

    g.addEdge('9', '6');
    g.addEdge('9', '7');

    //Print a string representation of the graph
    System.out.println(
      "**********The adjacency list representation of the graph:\n" +
      g.toString()
    );

    System.out.println("**********Breadth-first-traversal:");
    Set<Character> hs = g.breadthFirstTraversal('0');
    System.out.println();
    for (Character i : hs) {
      System.out.print(i + " ");
    }
    System.out.println();

    System.out.println("**********Breadth-first-traversal with Recursion:");
    hs = g.bfs('0');
    System.out.println();
    for (Character i : hs) {
      System.out.print(i + " ");
    }
    System.out.println();

    System.out.println(
      "**********Depth-first-traversal, go with right subtree first:"
    );
    hs = g.depthFirstTraversal('0');
    System.out.println();
    for (Character i : hs) {
      System.out.print(i + " ");
    }
    System.out.println();

    System.out.println(
      "**********Depth-first-traversal with Recursion, go with left subtree first:"
    );
    hs = g.dfs('0');
    System.out.println();
    for (Character i : hs) {
      System.out.print(i + " ");
    }
    System.out.println();
  }
}

class Graph<E> {

  private Map<E, List<E>> listMap;
  public boolean directed;

  public Graph() {
    directed = true;
    listMap = new HashMap<>();
  }

  public Graph(boolean directed) {
    this.directed = directed;
    listMap = new HashMap<>();
  }

  /*********** Add a vertex to the graph */
  public boolean addVertex(E v) throws Exception {
    if (listMap.containsKey(v)) {
      throw new Exception("Vertex exists");
    } else {
      listMap.put(v, new LinkedList<E>());
      return true;
    }
  }

  /*********** Add an Edge between two vertices */

  public void addEdge(E source, E destination) throws Exception {
    if ((this.directed == false) && hasEdge(destination, source)) {
      return;
    }
    if (!listMap.containsKey(source)) addVertex(source);

    if (!listMap.containsKey(destination)) addVertex(destination);

    listMap.get(source).add(destination);

    if (this.directed == false) {
      /* in this case add an edge from destination to source node */
      listMap.get(destination).add(source);
    }
  }

  /*********** Remove an edge from source to destination */

  public void removeEdge(E source, E dest) throws Exception {
    if (!listMap.containsKey(source)) {
      throw new Exception("source node not found");
    } else {
      listMap.get(source).remove(dest);
      if (this.directed == false) {
        listMap.get(dest).remove(source);
      }
    }
  }

  /*********************** Remove  a given vertex ******************/

  public void removeVertex(E vertex) {
    /**
	   (a) If the vertex is a key in the map, we need to remove the key
	   (b) We need to remove the vertex from all adjacency lists
	*/

    if (listMap.containsKey(vertex)) {
      listMap.remove(vertex);
      for (List l : listMap.values()) {
        if (l.contains(vertex)) {
          l.remove(vertex);
        }
      }
    } else {
      System.out.println("No vertex to remove");
      return;
    }
  }

  /******************** get an adjacency list of the given vertex */

  public List<E> getAdjacencyList(E v) {
    List<E> adjList = listMap.get(v);
    return adjList;
  }

  /*********** Method to return  number of vertices in the graph */

  public int nodeCount() {
    return listMap.keySet().size();
  }

  /***********  Method to return  number of  edges in the graph */

  public int edgeCount() {
    int count = 0;
    for (E v : listMap.keySet()) {
      count += listMap.get(v).size();
    }
    if (directed == false) {
      count = count / 2;
    }
    return count;
  }

  /*********** checks if graph contains given vertex */
  public boolean hasVertex(E s) {
    return listMap.containsKey(s);
  }

  /*********** checks if graph contains an edge between two vertices */
  public boolean hasEdge(E source, E dest) {
    if (!hasVertex(source) || !hasVertex(dest)) {
      return false;
    }
    return listMap.get(source).contains(dest);
  }

  /*********** Print adjacency list of a vertex */

  public String printAdjacencyList(E v) {
    List<E> adjList = getAdjacencyList(v);
    StringBuilder builder = new StringBuilder();
    for (E e : adjList) {
      builder.append(e.toString() + " ");
    }
    builder.append("\n");
    return builder.toString();
  }

  /*********** String representation of adjacency lists of each vertex */

  public String toString() {
    /** We could append using String object, but we use a StringBuilder instead.
	    See https://stackoverflow.com/questions/1532461/stringbuilder-vs-string-concatenation-in-tostring-in-java
	    for why that is better when you are appending to strings in a loop.
	*/

    StringBuilder builder = new StringBuilder();
    for (E v : listMap.keySet()) {
      builder.append(v.toString() + ": ");
      for (E w : listMap.get(v)) {
        builder.append(w.toString() + " ");
      }
      builder.append("\n");
    }
    return (builder.toString());
  }

  /*********** Depth-First-Traversal without Recursion**********/

  public Set<E> depthFirstTraversal(E startVertex) {
    Set<E> visited = new LinkedHashSet<E>();
    Stack<E> stack = new Stack<E>();
    stack.push(startVertex);
    while (!stack.isEmpty()) {
      E vertex = stack.pop();
      if (!visited.contains(vertex)) {
        visited.add(vertex);

        for (E v : this.getAdjacencyList(vertex)) {
          stack.push(v);
        }
      }
    }
    return visited;
  }

  /*********** Depth-First-Traversal with Recursion**********/
  public Set<E> dfs(E startVertex) {
    Set<E> visited = new LinkedHashSet<E>();
    return depthFirstRecursion(startVertex, visited);
  }

  //The recursive function of breath first traversal, finish this method
  public Set<E> depthFirstRecursion(E startVertex, Set<E> visited) {
    if (!visited.contains(startVertex)) {
      visited.add(startVertex);
      for (E v : this.getAdjacencyList(startVertex)) {
        depthFirstRecursion(v, visited);
      }
    }
    return visited;
  }

  /*********** Breadth-First-Traversal **********/

  public Set<E> breadthFirstTraversal(E startVertex) {
    Set<E> visited = new LinkedHashSet<E>();
    Queue<E> queue = new LinkedList<E>();
    queue.add(startVertex);
    while (!queue.isEmpty()) {
      E vertex = queue.poll();
      if (!visited.contains(vertex)) {
        visited.add(vertex);

        for (E v : this.getAdjacencyList(vertex)) {
          queue.add(v);
        }
      }
    }
    return visited;
  }

  /*********** Breadth-First-Traversal with Recursion**********/
  public Set<E> bfs(E startVertex) {
    Set<E> visited = new LinkedHashSet<E>();
    Queue<E> queue = new LinkedList<E>();
    queue.add(startVertex);
    return breathFirstRecursion(queue, visited);
  }

  //The recursive function of breath first traversal,finish this method

  public Set<E> breathFirstRecursion(Queue<E> queue, Set<E> visited) {
    // Use the right tree traversal method to implement this method
    if (!queue.isEmpty()) {
      E vertex = queue.poll();
      if (!visited.contains(vertex)) {
        visited.add(vertex);
        for (E v : this.getAdjacencyList(vertex)) {
          queue.add(v);
        }
      }
      return breathFirstRecursion(queue, visited);
    }
    return visited;
  }

  public void printLinkedHashSet(Set<E> lhs) {
    for (E e : lhs) {
      System.out.print(e + " ");
    }
    System.out.println();
  }
}