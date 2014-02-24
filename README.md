Facebook-Archive-Organizer
==========================

Facebook gives users the ability to export all of their profile data to a .zip file. However, the naming convention of the directories and files are just unique IDs. This script parses the .htm files that Facebook includes in the .zip file, renames the directories to match the album names and renames the files to be the timestamp of when the photos were taken (or uploaded to Facebook if there isn't meta data).

## Instructions: 

1. Follow these steps to get your profile data: https://www.facebook.com/help/212802592074644
2. Unzip your data
3. Create a destination directory
4. Update $config to have the correct paths to "source" and "destination"
5. Run the script
