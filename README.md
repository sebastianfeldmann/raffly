# Raffly - PHP Raffle Application

A simple PHP application for creating and managing raffles with unique URLs and a spinning wheel interface.

## Features

- **Create Raffles**: Simple form to create new raffles with custom titles
- **Unique URLs**: Each raffle gets a 5-character random URL like `/signup/efq3d`
- **Participant Registration**: People can join raffles by entering their name
- **Spinning Wheel**: Visual wheel of fortune to pick random winners
- **Admin Panel**: View and manage all raffles from `/admin/raffles`
- **QR-Code**: Generate QR-Code linking to the signup

## Requirements

- PHP 8.0+ (uses PHP 8 features like `random_int()`)
- Write permissions for the `/data` directory

## Installation

1. **Clone or download**

2. **Set up the document root** to point to the `/public` folder:
   ```
   /raffly/
   ├── /public/           (← This should be your document root)
   ├── /data/
   └── README.md
   ```

3. **Set data directory permissions**:
   ```bash
   chmod 755 data
   ```

4. **Configure Apache** to use `/public` as document root and enable mod_rewrite:
   ```apache
   <VirtualHost *:80>
       DocumentRoot /path/to/raffly/public
       <Directory /path/to/raffly/public>
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

5. **Access the application** at your domain

## Usage

### Creating a Raffle
1. Go to the homepage
2. Enter a title for your raffle
3. Click "Create Raffle"
4. You'll be redirected to the raffle URL

### Adding Participants
1. Share the signup URL (`/signup/efq3d`) with participants
2. Each person enters their name (max 20 characters)
3. Duplicate names are prevented automatically

### Running the Raffle
1. Go to the raffle wheel (`/raffle/efq3d`)
2. Click "Spin the Wheel!" to pick a random winner
3. Winners are moved to a separate list
4. You can spin again to pick additional winners

### Admin Management
1. Visit `/admin/raffles` to see all raffles
2. View participant counts and update dates
3. Delete raffles when no longer needed

## Data Format

Each raffle is stored as a JSON file with this structure:

```json
{
    "title": "My Awesome Raffle",
    "participants": ["Alice", "Bob", "Charlie"],
    "winners": ["Peter"]
}
```

## License

This project is open source. Feel free to use and modify as needed.
