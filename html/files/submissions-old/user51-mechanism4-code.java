package cpu;

import java.util.Scanner;

public class CPU {

    public static void main(String[] args) {
        Scanner scanner = new Scanner(System.in);
        int pn, i, j, temp, stTemp = 0, avg = 0;
        String temps;

        System.out.print("Enter No. of process: ");
        pn = scanner.nextInt();

        String[] p = new String[pn]; //Process number
        int[] at = new int[pn];     //Arrival time
        int[] et = new int[pn];    //Execute Time
        int[] st = new int[pn];   //Service Time

        System.out.println("Enter Arrival Time & Execute Time (Duration) of each Process");
        for (i = 0; i < pn; i++) {
            p[i] = "P" + i;
            System.out.print(p[i] + "  ");
            at[i] = scanner.nextInt();
            et[i] = scanner.nextInt();
        }
        for (i = 0; i < pn; i++) {              //Sorting the Process, Execute time base on ARRIVAL TIME (FCFS)
            for (j = 0; j < pn; j++) {
                if (i != j && at[i] < at[j]) {
                    temp = at[i];
                    at[i] = at[j];
                    at[j] = temp;

                    temp = et[i];
                    et[i] = et[j];
                    et[j] = temp;

                    temps = p[i];
                    p[i] = p[j];
                    p[j] = temps;
                }
            }
        }

        System.out.println("\nProcess No. |   Arrival Time  |   Execite Time   |    Service Time");
        for (i = 0; i < pn; i++) {
            st[i] = stTemp;  //Calculating Service Time
            try {
                stTemp = st[i] + et[i - 1];
            } catch (Exception e) {
            }
            st[i] = stTemp;
            avg += st[i] - at[i]; //putting (Service time - Arrival time) in a variable to calculate tht AVERAGE waiting time
            System.out.println("     " + p[i] + "     |        " + at[i] + "        |        " + et[i] + "         |        " + st[i]);
        }
        System.out.println("\nAverage waithing time : " + (double)(avg) / pn);
    }
}
