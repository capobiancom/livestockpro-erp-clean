import re
import sys

def convert_schema(input_file, output_file):
    with open(input_file, 'r') as f:
        sql = f.read()

    # Basic replacements
    sql = sql.replace('autoincrement', 'AUTO_INCREMENT')
    sql = sql.replace('integer primary key AUTO_INCREMENT', 'INT AUTO_INCREMENT PRIMARY KEY')
    sql = sql.replace('integer primary key', 'INT PRIMARY KEY')
    sql = sql.replace('datetime', 'DATETIME')
    
    # SQLite uses `varchar` without length, MySQL needs length
    sql = re.sub(r'varchar(?!\()', 'VARCHAR(255)', sql, flags=re.IGNORECASE)
    
    # text fields
    sql = re.sub(r'\btext\b', 'TEXT', sql, flags=re.IGNORECASE)
    
    # Boolean equivalent
    sql = re.sub(r'boolean', 'TINYINT(1)', sql, flags=re.IGNORECASE)

    # Double quotes to backticks
    sql = sql.replace('"', '`')
    
    # Remove sqlite_sequence related stuff
    sql = re.sub(r'DELETE FROM `sqlite_sequence`.*?;', '', sql)
    sql = re.sub(r'INSERT INTO `sqlite_sequence`.*?;', '', sql)
    
    with open(output_file, 'w') as f:
        f.write(sql)
        
if __name__ == "__main__":
    convert_schema(sys.argv[1], sys.argv[2])
