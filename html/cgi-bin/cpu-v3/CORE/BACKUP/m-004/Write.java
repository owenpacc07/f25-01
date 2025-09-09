import java.io.File;
import java.util.Scanner;
import java.io.FileWriter;
import java.io.IOException;

class Write{
    public static void main(String[] args){
        String schedulerType = args[0];
        String path = args[1];
        try{
            // Reading File
            File inputFile = new File(path);
            Scanner myReader = new Scanner(inputFile);
            String inputText = "";

            while (myReader.hasNextLine()) {
                inputText += myReader.nextLine()+"\n";
            }
            myReader.close();

            inputFile.delete();

            // Writing to file
            String[] arrText = inputText.split("\n");
            boolean first = true;

            FileWriter wr = new FileWriter(path);
            for (String strText : arrText) {
                if (first) {
                    first = false;
                    wr.write(schedulerType + strText.substring(1, strText.length()) + "\n");
                }
                else
                    wr.write(strText + "\n");
            }
            wr.close();
            
        } catch(IOException e){
            System.out.println("Error in Write.java");
        }
    }
}