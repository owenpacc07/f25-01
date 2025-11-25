1# Table of Contents

- [Table of Contents](#table-of-contents)
- [Introduction](#introduction)
- [Setting Up the Project](#setting-up-the-project)
  - [Windows](#windows)
  - [Linux/MacOS](#linuxmacos)
  - [Git Instructions](#git-instructions)
    - [Server Setup](#server-setup)
    - [Development Machine Setup](#development-machine-setup)
      - [Creating a local MySQL database user](#creating-a-local-mysql-database-user)
- [Conclusion](#conclusion)

# Introduction

For easier collaboration and sharing of code, git was installed and added to the project. For those unfamiliar with git, it is a version control system (VCS) that tracks changes in computer files. To use git on a machine, it must be installed. There are many ways to use git, and the command line interface is one of them. Apps such as GitKraken or Github Desktop are useful alternatives if the command line interface is too difficult to navigate. However, for this project, there are a couple of restrictions on such tools such as not being able to push changes due to requiring SSH.

For this project, the Git-CLI must be used to push changes to the server/production branch which is essentially the team’s shared CS folder. For basic commits, staging, and file tracking, it is fine to use GitHub Desktop or alternatives. Pushing/pulling requires the SSH protocol and a password, and pretty much only git CLI is capable of doing so, and is simple enough.

---

# Setting Up the Project

Setting up the project will include installing and setting up git, with the option for using some CLI tools or using a GUI like Github Desktop. Installation for git will be different depending on system. For use on Windows or Mac, follow the instructions [here](https://git-scm.com/downloads). Mac also has the option for using homebrew, if that is installed on your system. For Linux, use your package manager, but git probably shipped with your distribution. For installing Github Desktop, you can download that [here](https://desktop.github.com/) for Windows and Mac, and it is avaibable through Flathub for Linux.
In order to set up the project you will first need to clone the repository onto your local machine. This is covered in the git instructions file. After cloning the repo, you can add the repository to Github Desktop by selecting add repository, then selecting add existing repository. After selecting the repository, you should be all set to use Github Desktop with your project.

## Windows

> To use Github desktop on windows, you will need to generate a ssh key and uploud it to the CS shared server.
> here is how you can aporuach this:

1. Open Git Bash/terminal on the local machine.
2. Paste the text below, substituting in your email address.

```bash
ssh-keygen -t rsa -b 4096 -C "your.email@example.com"
```

This command creates an ssh key-pair

3. When you're prompted to "Enter a file in which to save the key," press Enter. This accepts the default file location.
4. At the prompt, press Enter to continue without a passphrase.
5. Run the following commands to add your private key to the SSH agent:

```bash
eval $(ssh-agent -s)
ssh-add ~/.ssh/id_rsa
```

6. Copy the SSH key to your clipboard.

```bash
ssh-copy-id username@cs.newpaltz.edu
```

7. Test that you can connect to the server with ssh.

```bash
ssh username@cs.newpaltz.edu
```

## Linux/MacOS

> **_Update if any differences are found_**

When using UNIX or Linux OSs, permissions maybe preventing the java code from running.

Thus, it is necessary to set the appropriate permissions.

Use this on htdocs, the repofolder, cgi-bin, files directories as necessary to change ownership of files/directory.

```zsh
sudo chown -R daemon:daemon [directory/file path]
```

After changing file ownership, set the correct permission. The command below will give the group and user read and write access (for cgi-bin/files)

```zsh
sudo chmod -R ug+rw [directory/file path]
```

For MacOS, XAMPP runs as the user daemon.
To check what XAMPP runs the server as, use whoami in a php page and echo that output.

The -R is a flag that can be set to recursively apply the changes.

These are some general instructions for using git. This is not an exhaustive list, and should be added to as new commands are utilized or new features of git are utilized.

## [Git Instructions](gitInstructions.md)

### Server Setup

**_For Future Groups_**

> Due to the way the professor moves files around into new groups folders, you will most likely not have the old git info. This means you may need to create a new git repository, and delete all of our old .git information. Good luck!

SSH into your newpaltz account using terminal.

```zsh
ssh <username>@cs.newpaltz.edu
```

Enter in your password, and then change directory to the shared CS folder of your team. Change ‘f23-05’ as needed for your group.

```zsh
cd /var/www/p/f23-05
```

Create a non-bare (default) repository in the shared CS folder. Make sure the terminal is in the correct location, change directory as needed.

```zsh
git init
```

Set the following config for the remote repo.

```zsh
git config receive.denyCurrentBranch updateInstead
```

Then, create a .env file using vim, nano, or use WinSCP. It must have the parameters found below and must be located at the root path of the shared CS folder: /var/www/p/f23-05/.env

Change the database login to match the team's database login information.

```ini
DB_USERNAME=p_f23_05
DB_PASSWORD=wasd123
DB_NAME=p_f23_05_db
SITE_ROOT="/p/f23-05"
```

The remote server is now properly configured and set.

### Development Machine Setup

Now, it is time to clone the repo to a development machine (the computer you will be coding with).

**_INSTALL XAMPP_** as it is the development environment used: it provides apache (webserver), php, and a local MySQL database. You may use an alternative if you know what you are doing. These steps will assume XAMPP being used, adapt as necessary.

Once XAMPP is installed, navigate to it's directory. There is a folder called **htdocs**, and this is where the git repo must be stored. htdocs is the directory XAMPP uses for websites.

**DELETE** everything inside htdocs but the index.php. Open the index.php and change it so it looks like this. You may need to change the last part of the header to match repo's name and current version.

```php
<?php
	if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
		$uri = 'https://';
	} else {
		$uri = 'http://';
	}
	$uri .= $_SERVER['HTTP_HOST'];
	header('Location: '.$uri.'/<repo_name>/html/v1');
	exit;
?>
Something is wrong with the XAMPP installation :-(
```

Open a new terminal. Run the git clone command below. Change the destination path as needed.

NOTE: You should run this command **locally** on your machine (do not run this while ssh'd into your New Paltz account). Either open a new terminal or exit out of the New Paltz session.

```git
git clone ssh://<username>@cs.newpaltz.edu:/var/www/projects/f23-05 [XAMPP_PATH]/htdocs/<repo_name>
```

This will download the repo, names it to whatever the repo_name is, and is located under XAMPPS' htdocs folder.

Open the downloaded repo and create another .env file. Its path should be `<XAMPP_PATH>/htdocs/<repo_name>/.env`.

In the .env file, update it to match your local machine's config. We will be using a local database provided by XAMPP, so this .env should be different from the server's .env file. We will be updating this to use our local database.

```ini
DB_USERNAME=johndoe
DB_PASSWORD=wasd123
DB_NAME=p_f23_05_db
SITE_ROOT="/<repo_name>/html"
```

#### Creating a local MySQL database user

Open XAMPP. Make sure the MySQL server is running. Navigate to `localhost/phpmyadmin`. At the top, click user accounts.

Create a new user, make a password, set host name to localhost, and give all permissions.

Update your local .env to match the new information.

## Java Files

When you make a change to a java file in the cgi-bin folder, it must be saved and recompiled.

To compile java code, run this in a terminal. Paths can be absolute or relative to the terminals current working directory.

```zsh
javac '/path/to/file.java'
```

I have included a script called java_compile that will compile all the java files. Compile using Java 17 as that is the current version of java installed on the wyvern server.

Here is the script below incase it does not get carried over.

java_compile
```
javac -cp ./html/cgi-bin/core/m-021 ./html/cgi-bin/core/m-021/m021.java;
javac -cp ./html/cgi-bin/core/m-044 ./html/cgi-bin/core/m-044/m044.java;
javac -cp ./html/cgi-bin/core/m-043 ./html/cgi-bin/core/m-043/m043.java;
javac -cp ./html/cgi-bin/core/m-011 ./html/cgi-bin/core/m-011/m011.java;
javac -cp ./html/cgi-bin/core/m-045 ./html/cgi-bin/core/m-045/m045.java;
javac -cp ./html/cgi-bin/core/m-042 ./html/cgi-bin/core/m-042/m042.java;
javac -cp ./html/cgi-bin/core/m-002 ./html/cgi-bin/core/m-002/m002.java;
javac -cp ./html/cgi-bin/core/m-005 ./html/cgi-bin/core/m-005/m005.java;
javac -cp ./html/cgi-bin/core/m-033 ./html/cgi-bin/core/m-033/m033.java;
javac -cp ./html/cgi-bin/core/m-032 ./html/cgi-bin/core/m-032/m032.java;
javac -cp ./html/cgi-bin/core/m-004 ./html/cgi-bin/core/m-004/m004.java;
javac -cp ./html/cgi-bin/core/m-003 ./html/cgi-bin/core/m-003/m003.java;
javac -cp ./html/cgi-bin/core/m-025 ./html/cgi-bin/core/m-025/m025.java;
javac -cp ./html/cgi-bin/core/m-022 ./html/cgi-bin/core/m-022/m022.java;
javac -cp ./html/cgi-bin/core/m-013 ./html/cgi-bin/core/m-013/m013.java;
javac -cp ./html/cgi-bin/core/m-041 ./html/cgi-bin/core/m-041/m041.java;
javac -cp ./html/cgi-bin/core/m-012 ./html/cgi-bin/core/m-012/m012.java;
javac -cp ./html/cgi-bin/core/m-023 ./html/cgi-bin/core/m-023/m023.java;
javac -cp ./html/cgi-bin/core/m-024 ./html/cgi-bin/core/m-024/m024.java;
javac -cp ./html/cgi-bin/core/m-006 ./html/cgi-bin/core/m-006/m006.java;
javac -cp ./html/cgi-bin/core/m-001 ./html/cgi-bin/core/m-001/m001.java;
javac -cp ./html/cgi-bin/core/m-008 ./html/cgi-bin/core/m-008/m008.java;
javac -cp ./html/cgi-bin/core/m-031 ./html/cgi-bin/core/m-031/m031.java;
javac -cp ./html/cgi-bin/core/m-007 ./html/cgi-bin/core/m-007/m007.java
```

---

# Conclusion

You know now how to set up the project, both on the server and for your local development machine.

The last step is to update these files for the group.

- `html/<version>/config-legacy.php` -- tried to condense all vars into one file, this remains as it hasnt been converted to use only the new config.php
- `html/<version>/config.php`
- `html/<version>/system.php`
