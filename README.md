# GTIN Product Database

A modern, responsive web application for managing and displaying product information with dynamic QR code generation. This application reads product data from a CSV file and provides an intuitive interface for searching, filtering, and viewing product QR codes.

**Webseite bereitgestellt von Nando.**

## Features

### 🔍 Search & Filter
- **Real-time search** - Search products by Product ID or Product Name
- **Category filters** - Filter products by:
  - MeinLand
  - PN (Product Name)
  - Bio
  - Demeter
  - Edeka
- **AND logic filtering** - Multiple filters show only products matching ALL selected categories
- **Live statistics** - Shows count of filtered vs total products
- **Prominent warning notice** - Important TU size comparison reminder for users

### 📱 QR Code Generation
- **Dynamic generation** - QR codes are generated on-the-fly from GTIN numbers
- **Lazy loading** - QR codes only load when visible in viewport for optimal performance
- **Interactive overlay** - Click any QR code to view an enlarged version
- **Dual overlay system** - Tap once to close enlarged QR code, tap again to close menu
- **High quality** - Generates crisp, scannable QR codes in PNG format

### 🎨 User Interface
- **Dark/Light mode** - Toggle between themes with persistent preference
- **Responsive design** - Optimized for desktop, tablet, and mobile devices
- **Mobile optimized** - Compact buttons, 2-column filter grid on mobile
- **Modern styling** - Clean, professional interface with smooth transitions
- **Accessible** - Keyboard navigation support (ESC to close overlay)
- **Warning alerts** - Prominent yellow warning box for important notices

### ⚡ Performance
- **Lazy loading** - QR codes load only when needed
- **Caching** - API responses cached for 24 hours
- **Optimized rendering** - Efficient table rendering with minimal reflows
- **Duplicate removal** - Automatically removes duplicate entries

## Technology Stack

- **Frontend**: HTML5, CSS3, Vanilla JavaScript
- **Backend**: PHP 8.1
- **QR Generation**: phpqrcode library
- **Web Server**: Nginx
- **Data Format**: CSV

## Installation

### Prerequisites

- Nginx web server
- PHP 8.1 or higher with GD extension
- Access to `/var/www/qr/phpqrcode/` library

### Setup Steps

1. **Clone the repository**
   ```bash
   cd /var/www
   git clone https://github.com/PrevosHD/GtinWebsite.git gtin
   ```

2. **Ensure CSV file exists**
   ```bash
   # Make sure output.csv is in the gtin directory
   ls -la /var/www/gtin/output.csv
   ```

3. **Configure Nginx**
   ```bash
   # Copy the nginx configuration
   sudo cp /var/www/gtin/gtin.conf /etc/nginx/sites-available/gtin.conf
   
   # Create symbolic link
   sudo ln -s /etc/nginx/sites-available/gtin.conf /etc/nginx/sites-enabled/gtin.conf
   
   # Test nginx configuration
   sudo nginx -t
   
   # Reload nginx
   sudo systemctl reload nginx
   ```

4. **Set proper permissions**
   ```bash
   sudo chown -R www-data:www-data /var/www/gtin
   sudo chmod -R 755 /var/www/gtin
   ```

5. **Verify PHP-FPM is running**
   ```bash
   sudo systemctl status php8.1-fpm
   ```

### DNS Configuration

Point your domain to the server:
```
gtin.bocchi.network -> Your Server IP
```

## File Structure

```
/var/www/gtin/
├── index.html          # Main application interface
├── api.php            # QR code generation API endpoint
├── output.csv         # Product database (CSV format)
├── gtin.conf          # Nginx configuration
└── README.md          # This file
```

## CSV Data Format

The application expects a CSV file with the following structure:

```csv
Product_ID,Product_Name,Location,GTIN,Status
12907558,PN Schnittlauch Bund,CH-Z-034-04-1,4260046983507,SUCCESS
12231169,PN Avocado 1 Stück,CH-D-014-01-3,4311532235126,SUCCESS
```

**Note**: Only `Product_ID`, `Product_Name`, and `GTIN` columns are used. `Location` and `Status` are ignored.

## API Documentation

### QR Code Generation Endpoint

**Endpoint**: `/api.php`

**Method**: GET

**Parameters**:
- `text` (required) - The text/GTIN to encode in the QR code
- `size` (optional) - Size of the QR code image in pixels (default: 200, min: 100, max: 1000)

**Example**:
```
https://gtin.bocchi.network/api.php?text=4260046983507&size=300
```

**Response**: PNG image

**Caching**: 24 hours (86400 seconds)

## Usage

1. **Access the application**
   - Open your browser and navigate to the configured domain

2. **Search for products**
   - Type in the search box to filter by Product ID or Name
   - Results update in real-time

3. **Apply filters**
   - Check category boxes to filter by product type
   - Multiple filters can be active simultaneously

4. **View QR codes**
   - Scroll through the table - QR codes load automatically
   - Click any QR code to view enlarged version
   - **Dual overlay interaction**:
     - First tap on dark background → Closes enlarged QR code
     - Second tap on dark background → Closes the menu
   - Press ESC to close overlay

5. **Toggle theme**
   - Click the theme button in the header
   - Preference is saved automatically

6. **Easter Egg Menu (Outbound)**
   - Hidden menu accessible via easter egg button
   - Contains pre-configured QR codes for common shipment types
   - Background darkens when menu is open
   - Same dual overlay interaction for viewing enlarged QR codes

## Browser Support

- Chrome/Edge 90+
- Firefox 88+
- Safari 14+
- Mobile browsers (iOS Safari, Chrome Mobile)

## Performance Considerations

- QR codes are generated on-demand using lazy loading
- Intersection Observer API ensures optimal performance
- No QR code images are stored on disk
- CSV parsing happens client-side for fast filtering
- Duplicate entries are automatically removed

## Troubleshooting

### QR codes not loading
- Check PHP-FPM is running: `sudo systemctl status php8.1-fpm`
- Verify phpqrcode library exists: `ls -la /var/www/qr/phpqrcode/`
- Check nginx error logs: `sudo tail -f /var/log/nginx/gtin.bocchi.network.error.log`

### CSV not loading
- Verify file exists and has correct permissions
- Check browser console for errors
- Ensure CSV format matches expected structure

### Nginx 502 errors
- Restart PHP-FPM: `sudo systemctl restart php8.1-fpm`
- Check socket path in gtin.conf matches your PHP-FPM configuration

## License

This project is open source and available for use and modification.

## Contributing

Contributions are welcome! Please feel free to submit pull requests or open issues.

## Author

PrevosHD

## Repository

https://github.com/PrevosHD/GtinWebsite

