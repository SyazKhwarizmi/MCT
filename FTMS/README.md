# FTMS - Fishing Tournament Management System

A web-based tournament management system that integrates with Google Sheets for data management.

## Setup Instructions

### 1. Get Your Google Sheets API Key

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Enable the Google Sheets API
4. Create credentials (API Key)
5. Restrict the API key to Google Sheets API only (recommended for security)

### 2. Configure the Application

1. Open `index.html` in your browser
2. Click on "⚙️ Configure Google Sheets"
3. Enter your:
   - **Google Sheets API Key**: Your API key from Google Cloud Console
   - **Google Sheet ID**: The ID from your Google Sheets URL
   - **Sheet Names**: Names of your sheet tabs (Players, Team, Boat, submission_dev)
4. Click "📊 Save & Load All Sheets"

The configuration will be saved in your browser's localStorage and will persist across sessions.

### 3. Security Notes

⚠️ **IMPORTANT**: 
- Never commit your API keys to GitHub or any public repository
- The `.gitignore` file is configured to exclude sensitive configuration files
- API keys are stored locally in your browser's localStorage
- For production, consider using environment variables or a backend service

## Features

- 📊 **Dashboard**: View all sheets and data
- 🐟 **Submissions Leaderboard**: Individual fish submissions ranked by length
- 👤 **Individual Leaderboard**: Player rankings with multiple categories
- 🏅 **Team Leaderboard**: Team rankings and statistics

## Categories in Individual Leaderboard

- **All Categories**: All players sorted by best fish
- **Best Of Lady Angler**: Female players (Gender = "F") sorted by submissions
- **Best of Kayaker**: Players from Kayak teams sorted by submissions
- **Best Of International Angler**: International players sorted by submissions
- **Best of Sponsorship Team**: Teams with sponsored = "Y" sorted by team submissions

## File Structure

```
FTMS/
├── index.html                    # Main dashboard
├── leaderboard.html              # Submissions leaderboard
├── leaderboard-individual.html   # Individual player leaderboard
├── leaderboard-team.html         # Team leaderboard
├── .gitignore                    # Git ignore file (excludes sensitive data)
├── config.example.js             # Configuration template
└── README.md                     # This file
```

## Troubleshooting

### API Key Issues
- Make sure your API key has access to Google Sheets API
- Check that your Google Sheet is shared with "Anyone with the link" or the API key has proper permissions

### Data Not Loading
- Verify your Sheet ID is correct
- Check that sheet names match exactly (case-sensitive)
- Ensure your API key is valid and not restricted incorrectly

## License

This project is for tournament management purposes.

