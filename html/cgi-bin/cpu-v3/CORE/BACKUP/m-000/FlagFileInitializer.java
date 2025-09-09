import java.io.File;
import java.io.FileWriter;
import java.io.IOException;

class FlagFileInitializer {

    public FlagFileInitializer() {
        super();
    }

    // updates flag file to have a value of 1
    public void updateFlagFile() {

        try {

            File flagFile = new File("flag-file.txt");
            FileWriter writer = new FileWriter(flagFile, false);
            writer.write(1);
            writer.close();
            
        } catch (IOException e) {
            System.out.println("Error in updateFlagFile()");
        }

    }

}