import sys
import json
import asyncio
# pyrefly: ignore [missing-import]
from fastmcp import FastMCP
import os

# Backup original argv
original_argv = sys.argv.copy()
# Clear sys.argv so argparse in linkedin_mcp_server doesn't parse it
sys.argv = [sys.argv[0]]

# Menambahkan directory root agar dapat mengimport modul MCP server
sys.path.append(os.path.abspath(r"C:\Users\ACER\.gemini\antigravity-ide\brain\93fbe52d-986f-48aa-9de6-04af7332ef83\scratch\linkedin-mcp-server\src"))

# pyrefly: ignore [missing-import]
from linkedin_mcp_server.dependencies import get_ready_extractor

async def main():
    if len(original_argv) < 2:
        print(json.dumps({"error": "LinkedIn username atau URL tidak diberikan."}))
        sys.exit(1)
        
    identifier = original_argv[1]
    
    try:
        # Mendapatkan extractor yang sudah terotentikasi. 
        # Jika belum login, ini akan otomatis membuka browser headful dan meminta login.
        extractor = await get_ready_extractor(ctx=None, tool_name="get_person_profile")
        
        # Mengekstrak data (tambahkan list sections yang diinginkan)
        # Main profile page selalu terekstrak
        profile_data = await extractor.scrape_person(identifier, {"experience", "education", "contact_info"})
        
        # Ekstrak data yang diperlukan
        about_ref = profile_data.get("references", {}).get("about", {})
        
        current_job = about_ref.get("headline", "")
        current_company = about_ref.get("current_company", "")
        location = about_ref.get("location", "")
        industry = about_ref.get("industry", "")
        
        # Fallback to parsing raw text if references are empty
        if not current_job:
            main_profile_text = profile_data.get("sections", {}).get("main_profile", "")
            if main_profile_text:
                lines = [line.strip() for line in main_profile_text.split('\n') if line.strip()]
                if len(lines) > 1:
                    current_job = lines[1]
                    # Attempt to extract company from headline (e.g. "Product Engineer at AstraPay")
                    if " at " in current_job:
                        current_company = current_job.split(" at ")[-1]
                if len(lines) > 2:
                    location = lines[2]
        
        result = {
            "current_job": current_job,
            "current_company": current_company,
            "location": location,
            "industry": industry,
            "raw_output": json.dumps(profile_data)
        }
        print(json.dumps(result))
    except Exception as e:
        print(json.dumps({"error": str(e)}))
        sys.exit(1)

if __name__ == "__main__":
    asyncio.run(main())
